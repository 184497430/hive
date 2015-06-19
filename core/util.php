<?php
/**
 * Created by PhpStorm.
 * User: jianglong
 * Date: 14-5-21
 * Time: 下午3:23
 */


if(!function_exists('get_called_class')) {
    class class_tools
    {
        private static $i = 0;
        private static $fl = null;
        public static function get_called_class()
        {
            $bt = debug_backtrace();
            //使用call_user_func或call_user_func_array函数调用类方法，处理如下
            if (array_key_exists(3, $bt)
                && array_key_exists('function', $bt[3])
                && in_array($bt[3]['function'], array('call_user_func', 'call_user_func_array'))
            ) {
                //如果参数是数组
                if (is_array($bt[3]['args'][0])) {
                    $toret = $bt[3]['args'][0][0];
                    return $toret;
                }else if(is_string($bt[3]['args'][0])) {//如果参数是字符串
                    //如果是字符串且字符串中包含::符号，则认为是正确的参数类型，计算并返回类名
                    if(false !== strpos($bt[3]['args'][0], '::')) {
                        $toret = explode('::', $bt[3]['args'][0]);
                        return $toret[0];
                    }
                }
            }
            //使用正常途径调用类方法，如:A::make()
            if(self::$fl == $bt[2]['file'].$bt[2]['line']) {
                self::$i++;
            } else {
                self::$i = 0;
                self::$fl = $bt[2]['file'].$bt[2]['line'];
            }
            $lines = file($bt[2]['file']);
            preg_match_all('/([a-zA-Z0-9\_]+)::'.$bt[2]['function'].'/',
                $lines[$bt[2]['line']-1],
                $matches
            );
            return $matches[1][self::$i];
        }
    }

    function get_called_class()
    {
        return class_tools::get_called_class();
    }
}

/**
 * 检查日期合法性
 * @param string $date 日期
 * @return bool
 */
function check_date($date) {
	$ret = FALSE;
	preg_match('#^(\d{4})(/|-)(\d{1,2})\2(\d{1,2})$#', $date, $arr);
	if (is_array($arr) && count($arr) == 5) {
		$ret = checkdate($arr[3], $arr[4], $arr[1]);
	}

	return $ret;
}

/**
 * 获取显示时间
 * @param int|string $timestamp 日期
 * @return string
 */
function show_human_time($timestamp) {
	$time_offset = time() - $timestamp;

	if ($time_offset < 3600) {
		return ($time_offset <= 0 ? '1' : ceil($time_offset / 60)) . '分钟前';
	} else {
		$today_time     = strtotime(date('Y-m-d'));
		$yesterday_time = strtotime(date('Y-m-d', time() - 3600 * 24));
		if ($timestamp > $today_time) {
			return '今天 ' . date('H:i', $timestamp);
		} else if ($timestamp > $yesterday_time) {
			return '昨天 ' . date('H:i', $timestamp);
		} else {
			return date('Y-m-d H:i', $timestamp);
		}
	}
}

function p($info, $exit = TRUE, $ret = FALSE) {
	if (defined('APP_ENV') && APP_ENV == 'development') {
		$debug  = debug_backtrace();
		$output = '';

		if (is_cli()) {
			$output .= '[TRACE]' . PHP_EOL;
			foreach ($debug as $v) {
				$output .= 'File:' . $v['file'];
				$output .= 'Line:' . $v['line'];
				$output .= 'Args:' . implode(',', $v['args']) . PHP_EOL;
			}
			$output .= '[Info]' . PHP_EOL;
			$output .= var_export($info, TRUE) . PHP_EOL;
		} else {
			foreach ($debug as $v) {
				$output .= '<b>File</b>:' . $v['file'] . '&nbsp;';
				$output .= '<b>Line</b>:' . $v['line'] . '&nbsp;';
				$output .= $v['class'] . $v['type'] . $v['function'] . '(\'';
				//$output .= implode('\',\' ', $v['args']);
				$output .= '\')<br/>';
			}
			$output .= '<b>Info</b>:<br/>';
			$output .= '<pre>';
			$output .= var_export($info, TRUE);
			$output .= '</pre>';
		}

		if ($ret) {
			return $output;
		} else {
			echo $output;
		}
		if ($exit) {
			exit;
		}
	} else {
		return;
	}
}

/**
 * 判断网址是否合法
 * @param string $url 网址
 * @return bool
 */
function is_url($url) {
	return preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i', $url) ? TRUE : FALSE;
}

/**
 * 通过curl get数据
 * @param string     $url        接口地址
 * @param int        $timeout    超时秒数
 * @param string     $user_agent user agent
 * @param int|string $erron      错误码
 * @return mixed|string
 */
function curl_get_contents($url, $timeout = 1, $user_agent = 'Mozilla/4.0', &$errno = '') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent . '+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE);
	curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
	$content = curl_exec($ch);
	if ($errno = curl_errno($ch)) {
		$info = curl_getinfo($ch);
		Core::sysLog('err_exec_curl_get', array(
			'errno'   => $errno,
			'error'   => curl_error($ch),
			'url'     => $info['url'],
			'code'    => $info['http_code'],
			'ut'      => $info['total_time'],
			'conn_ut' => $info['connect_time'],
			'nl_ut'   => $info['namelookup_time'],
		));
	}
	curl_close($ch);

	return $content;
}

/**
 * 请求直播中心数据
 * @param        $path
 * @param array  $data
 * @param string $uri
 * @return array
 */
function post_live_interface($path, $data = array(), $time_out = 2, $uri = 'http://api.live.qiyi.domain') {
	$data['clientTime'] = time();

	$secret  = 'tfdoe3xU0M8eQg9W';
	$sign    = md5($data['clientTime'] . $path . $secret);
	$auth    = "show:" . $sign;
	$start   = microtime(TRUE);
	$content = curl_post_contents($uri . $path, $data, $time_out, '', array("Authorization: " . $auth));
	$result  = json_decode($content, 1);
	post_live_stat($path, $start);
	if (!$content || !$result || !isset($result['code']) || $result['code'] != 'A00000') {
		Core::sysLog('err_live_interface', array(
			'interface' => $path,
			'params'    => $data,
			'secret'    => array('secret' => $secret, 'time' => $data['clientTime'], 'path' => $path, 'auth' => $auth),
			'return'    => $content,
		));
	}

	return $result;
}

function post_live_stat($interface, $start) {
	$redis = RedisFactory::getInstance('cache_stat');
	$redis->hIncrBy('live_api:' . date('YmdH'), $interface, 1);
	$_ut = microtime(TRUE) - $start;
	$redis->hIncrByFloat('live_apiut:' . date('YmdH'), $interface, $_ut);
	if ($_ut > 1) {
		$redis->hIncrBy('live_apitimeout:' . date('YmdH'), $interface, 1);
	}
}

/**
 * 通过curl post数据
 * @param string $url        接口地址
 * @param array  $data       post参数
 * @param int    $timeout    超时秒数
 * @param string $cookiepath COOKIE的存储路径
 * @return mixed|string
 */
function curl_post_contents($url, $data = array(), $timeout = 1, $cookiepath = '', $header = array()) {
	if (!is_array($data) || !$url) {
		return '';
	}
	$referer = $url;
	$post    = $data ? http_build_query($data) : '';
	$ch      = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)');
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
	if ($header) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	$content = curl_exec($ch);
	if ($errno = curl_errno($ch)) {
		$info = curl_getinfo($ch);
		Core::sysLog('err_exec_curl_post', array(
			'errno'   => $errno,
			'error'   => curl_error($ch),
			'url'     => $info['url'],
			'code'    => $info['http_code'],
			'ut'      => $info['total_time'],
			'conn_ut' => $info['connect_time'],
			'nl_ut'   => $info['namelookup_time'],
		));
	}
	curl_close($ch);

	return $content;
}

/**
 * 通过curl put数据
 * @param string $url       接口地址
 * @param string $file_path 上传文件路径
 * @param int    $timeout   超时秒数
 * @param array  $header    追加的header头
 * @return  string
 */
function curl_put_file($url, $file_path, $timeout = 5, $header = array()) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_PUT, TRUE);
	$h = fopen($file_path, 'r');
	curl_setopt($ch, CURLOPT_INFILE, $h);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file_path));

	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
	if ($header) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	$content = curl_exec($ch);
	if ($errno = curl_errno($ch)) {
		$info = curl_getinfo($ch);
		Core::sysLog('err_exec_curl_put', array(
			'errno'   => $errno,
			'error'   => curl_error($ch),
			'url'     => $info['url'],
			'code'    => $info['http_code'],
			'ut'      => $info['total_time'],
			'conn_ut' => $info['connect_time'],
			'nl_ut'   => $info['namelookup_time'],
		));
	}
	curl_close($ch);
	fclose($h);

	return $content;
}

/**
 * 获取当前HOST
 * @param void
 * @return string
 */
function get_http_host() {
	return "http://" . (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
}

/**
 * 跳转页面重定向
 * @param string $url    网址
 * @param int    $status 状态码
 * @return void
 */
function redirect($url, $status = 302) {
	header('Location: ' . $url, TRUE, $status);
	exit();
}

//礼物特效
function get_swf_effect($origin_swf) {
	if (strlen($origin_swf) == 0) {
		return '';
	}
	static $flash_effect_list = array(
		'wp_lw_jinhuatong_50x50.swf'     => array(188, 520, 1314),
		'wp_lw_xiang_50x50.swf'          => array(188, 520, 1314),
		'wp_lw_guzhang_50x50.swf'        => array(188, 520, 1314),
		'wp_lw_zhibeidangao_50x50.swf'   => array(188, 520, 1314),
		'wp_lw_wanjuxiong_50x50.swf'     => array(188, 520, 1314),
		'wp_lw_shendanlaoren_50x50.swf'  => array(188, 520, 1314),
		'wp_lw_zuanshi_50x50.swf'        => array(188, 520, 1314),
		'wp_lw_huangguan_50x50.swf'      => array(188, 520, 1314),
		'wp_lw_labasuan_50x50.swf'       => array(188, 520, 1314),
		'wp_lw_labazhou_50x50.swf'       => array(188, 520, 1314),
		'wp_lw_bangbangtang_50x50.swf'   => array(188, 520, 1314),
		'wp_lw_aixin_50x50.swf'          => array(188, 520, 1314),
		'wp_lw_qiaokeli_50x50.swf'       => array(188, 520, 1314),
		'wp_lw_pingguo_50x50.swf'        => array(188, 520, 1314),
		'wp_lw_meigui_50x50.swf'         => array(188, 520, 1314),
		'wp_lw_mingpaibao_50x50.swf'     => array(188, 520, 1314),
		'wp_lw_hongbao_50x50.swf'        => array(188, 520, 1314),
		'wp_lw_zhuguangwancan_50x50.swf' => array(1),
		'wp_lw_fangka_50x50.swf'         => array(1),
		'wp_lw_qingshu_50x50.swf'        => array(1),
		'wp_lw_yuanxiao_50x50.swf'       => array(188, 520, 1314),
		'wp_lw_huadeng_50x50.swf'        => array(188, 520, 1314),
	);
	$flash_effect_map = array_keys($flash_effect_list);
	foreach ($flash_effect_map as $flash_map_key) {
		if (strpos($origin_swf, $flash_map_key) !== FALSE) {
			return implode(',', $flash_effect_list[$flash_map_key]);
		}
	}

	return '';
}

function get_product_cate($cate_id) {
	static $map = array(
		1 => '礼物',
		2 => '道具',
		3 => '贵族',
		4 => '守护',
		5 => '靓号',
		6 => '礼包',
	);

	return isset($map[$cate_id]) ? $map[$cate_id] : '未知';
}

/**
 * 判读是否是 Ajax 请求
 * @param void
 * @return bool
 */
function is_ajax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
}

/**
 * 格式化时间
 * @param  $time 时间妙
 * @return string
 */
function format_time($time) {
	$d = $time / 60;
	if ($d > 60) {
		$d = floor($d / 60) . "小时" . ($d % 60) . "分钟";
	} else {
		$d = ceil($d) . "分钟";
	}

	return $d;
}

/**
 * 格式化Schema视频时长
 * @param $time 时常
 * @return string
 */
function format_schema_time_duration($time) {
	$time    = $time / 1000;
	$hours   = floor($time / 3600);
	$time    = $time % 3600;
	$minutes = floor($time / 60);
	$second  = floor($time % 60);
	if ($hours < 10) {
		$hours = '0' . $hours;
	}
	if ($minutes < 10) {
		$minutes = '0' . $minutes;
	}
	if ($second < 10) {
		$second = '0' . $second;
	}

	return 'P' . $hours . 'H' . $minutes . 'M' . $second . 'S';
}

/**
 * 是否命令行运行
 * @param void
 * @return bool
 */
function is_cli() {
	return PHP_SAPI == 'cli' && empty($_SERVER['REMOTE_ADDR']);
}

/**
 * 获取当前页面URL
 * @param void
 * @return string
 */
function curr_url() {
	$url = 'http';

	if ($_SERVER["HTTPS"] == "on") {
		$url .= "s";
	}
	$url .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {
		$url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}

	return $url;
}

/**
 * GET方式调用HTTP接口，含重试机制
 * @param string $url      接口地址
 * @param int    $max_num  重试次数
 * @param int    $min_time 超时时间
 * @return mixed|string
 */
function http_get_contents($url, $max_num = 1, $min_time = 1) {
	$str = '';
	$i   = 0;

	while (empty($str) && $i < $max_num) {
		$str = curl_get_contents($url, $min_time);
		$i++;
	}

	return $str;
}

/**
 * POST方式调用HTTP接口，含重试机制
 * @param string $url      接口地址
 * @param array  $param    参数
 * @param int    $max_num  重试次数
 * @param int    $min_time 超时时间
 * @return mixed|string
 */
function http_post_contents($url, $param = array(), $max_num = 1, $min_time = 2) {
	$str = '';
	$i   = 0;
	while (empty($str) && $i < $max_num) {
		$str = curl_post_contents($url, $param, $min_time);
		$i++;
	}

	return $str;
}

/**
 * 判断是否为搜索引擎蜘蛛
 * @param void
 * @return bool
 */
function is_search_bot() {
	$bots       = array(
		'Google' => 'Googlebot',
		'Baidu'  => 'Baiduspider',
		'Yahoo'  => 'Yahoo! Slurp',
		'Soso'   => 'Sosospider',
		'Msn'    => 'msnbot',
		'Sogou'  => 'Sogou spider',
		'Yodao'  => 'YodaoBot'
	);
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	foreach ($bots as $k => $v) {
		if (stristr($v, $user_agent)) {
			return $k;
			break;
		}
	}

	return FALSE;
}

/**
 * 判断设备信息
 * @param $which 设备名all android ,ios
 * @return bool
 */
function device_detect($which = 'all') {
	$agent      = strtolower($_SERVER['HTTP_USER_AGENT']);
	$is_android = strpos($agent, 'android') !== FALSE ? TRUE : FALSE;
	$is_ios     = strpos($agent, 'iphone') !== FALSE || strpos($agent, 'ipad') !== FALSE || strpos($agent, 'ipod') !== FALSE ? TRUE : FALSE;
	if ($which == 'android') {
		return $is_android;
	} elseif ($which == 'ios') {
		return $is_ios;
	} elseif ($which == 'all') {
		return $is_android || $is_ios;
	} else {
		return FALSE;
	}
}

/**
 * UGC判断字符串是否含有特殊字符
 * @param $content 输入字符
 * @return bool
 */
function ugc_filter($content) {
	$trans = array(
		"（" => '',
		"~" => '',
		"｀" => '',
		"@" => '',
		"#" => '',
		"^" => '',
		"&" => '',
		"*" => '',
		"+" => '',
		"=" => '',
		"＼" => '',
		"／" => '',
		"）" => ''
	);

	return strtr($content, $trans);
}

/**
 * 优化显示万、亿等级的数字，小数保留两位
 * @param  int $num 输入数字
 * @return int
 */
function number_adv_show($num) {
	$num = (int) $num;
	if ($num < 10000) {
		return $num;
	} elseif ($num >= 10000 && $num < 100000000) {
		return (floor($num * 10 / 10000) / 10) . '万';
	} else {
		return (floor($num * 10 / 100000000) / 10) . '亿';
	}
}

/**
 * 对二维数组,按第二维数组中的某个键名对进行排序
 * @param array  $arr
 * @param string $field 排序的键名
 * @return array
 */
function array_sort($arr, $field, $sort_flags = 0) {
	$sort_tmp = array();
	$arr_tmp  = array();
	foreach ($arr as $key => $value) {
		$sort_tmp[$key] = $value[$field];
	}
	asort($sort_tmp);
	foreach ($sort_tmp as $k => $v) {
		$arr_tmp[] = $arr[$k];
	}

	return $sort_flags ? array_reverse($arr_tmp) : $arr_tmp;
}

/**
 * 获取用户ip地址
 * @return mixed
 */
function get_user_ip() {
	if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
		$ip = trim($_SERVER['HTTP_X_REAL_IP']);
	} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = trim($_SERVER['HTTP_CLIENT_IP']);
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip_arr = explode(',', trim($_SERVER['HTTP_X_FORWARDED_FOR']));
		$ip     = $ip_arr && count($ip_arr) > 0 ? trim($ip_arr[0]) : 'err';
	} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip = trim($_SERVER['REMOTE_ADDR']);
	} else {
		$ip = 'err';
	}

	return $ip;
}

function get_host_name() {
	return $_SERVER['HOSTNAME'] ? $_SERVER['HOSTNAME'] : php_uname('n');
}

/**
 * 设置返回码
 * @param        $code
 * @param string $msg
 * @param string $data
 * @param string $format
 * @param bool   $use_json_object
 * @return void
 */
function setResponseData($code, $msg = '', $data = '', $format = 'json', $use_json_object = FALSE) {
	$result = array(
		'code' => $code,
		'msg'  => $msg,
		'data' => $data
	);
	if ($format == 'json') {
		header("Content-type:application/json;charset=utf-8");
		echo json_encode($result, $use_json_object ? JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT : JSON_UNESCAPED_UNICODE);
	}
}

//取得URL的主域名
function is_pps_domain($url) {
	$tmp         = parse_url($url);
	$tmp_host    = array_reverse(explode('.', $tmp['host']));
	$host_domain = $tmp_host[1] . '.' . $tmp_host[0];
	if ($host_domain == 'pps.tv') {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * 全部转换成字符串
 * @param        $param
 * @param string $secret
 * @return string
 */
function dataToString($data) {
	if (is_array($data)) {
		array_walk_recursive($data, '_toString');
	} elseif (is_object($data)) {
		array_walk_recursive(get_object_vars($data), '_toString');
	} else {
		$data = (string) $data;
	}

	return $data;
}

function  _toString(&$value) {
	$value = (string) $value;
}

/**
 * 生成API接口Sign值
 * @param        $param
 * @param string $secret
 * @return string
 */
function generateApiSign($param, $secret = '') {
	$sign = '';
	ksort($param);
	unset($param['sign']);
	foreach ($param as $key => $value) {
		if (is_string($value) || is_numeric($value)) {
			$sign .= $key . $value;
		}
	}

	$sign_type = isset($param['sign_type']) ? strtolower($param['sign_type']) : '';
	switch ($sign_type) {
		case 'md5':
			$sign = md5($sign . $secret);
			break;
		case 'salt':
			$sign = md5(substr(md5($secret), 5, 17) . $sign . $secret . substr(md5($secret), 3, 23));
			break;
		default:
			$sign = sha1($sign . $secret);
			break;
	}

	return $sign;
}

/**
 * 渲染模版
 * @param string $name 模版名
 * @param array  $vars 模版变量
 * @return void
 */
function Template_show($name, array $vars = NULL) {
	$file = APP_ROOT . DS . 'view' . DS . strtolower($name) . '.view.php';
	if (isset($vars)) {
		extract($vars);
	}

	require($file);
}

/**
 * 获取js和css的样式地址
 * @param        $file_path      文件路径
 * @param string $js_css_version 版本号
 * @param string $js_css_version 版本号
 * @return string
 */
function get_static_path($file_path, $js_css_version, $type = 'css') {
	$style_path = core::getConfig('style_path');

	return ($type == 'js' ? $style_path['js_path'] : $style_path['css_path']) . $file_path . '?v=' . $js_css_version;
}

/**
 * This function is ...
 * @param string $birthday
 * @return string
 */
function get_constellation($birthday = '') {
	if (empty($birthday)) {
		return FALSE;
	}

	$month = date('n', strtotime($birthday));
	$day   = date('j', strtotime($birthday));
	$signs = array(
		array('20' => '水瓶座'),
		array('19' => '双鱼座'),
		array('21' => '白羊座'),
		array('20' => '金牛座'),
		array('21' => '双子座'),
		array('22' => '巨蟹座'),
		array('23' => '狮子座'),
		array('23' => '处女座'),
		array('23' => '天秤座'),
		array('24' => '天蝎座'),
		array('22' => '射手座'),
		array('22' => '摩羯座')
	);
	$key   = (int) $month - 1;
	list($startSign, $signName) = each($signs[$key]);
	if ($day < $startSign) {
		$key = $month - 2 < 0 ? $month = 11 : $month -= 2;
		list($startSign, $signName) = each($signs[$key]);
	}

	return $signName;
}

/**
 * 获取各种等级映射表
 * @param  $type
 * @return array
 */
function get_level_maps($type) {
	$level = array(
		'user_level'     => array(
			'1'  => 0,
			'2'  => 100,
			'3'  => 300,
			'4'  => 600,
			'5'  => 1100,
			'6'  => 2100,
			'7'  => 4100,
			'8'  => 7100,
			'9'  => 11100,
			'10' => 16100,
			'11' => 22100,
			'12' => 29100,
			'13' => 37100,
			'14' => 46100,
			'15' => 56100,
			'16' => 76100,
			'17' => 106100,
			'18' => 146100,
			'19' => 196100,
			'20' => 256100,
			'21' => 326100,
			'22' => 406100,
			'23' => 496100,
			'24' => 596100,
			'25' => 746100,
			'26' => 946100,
			'27' => 1196100,
			'28' => 1496100,
			'29' => 1846100,
			'30' => 2346100,
			//			'31' => 2946100,
			//			'32' => 3646100,
			//			'33' => 4446100,
			//			'34' => 5346100,
			//			'35' => 6346100,
			//			'36' => 7446100,
			//			'37' => 8646100,
			//			'38' => 9946100,
			//			'39' => 11346100,
			//			'40' => 12846100,
			//			'41' => 14446100,
			//			'42' => 16146100,
			//			'43' => 17946100,
			//			'44' => 19846100,
			//			'45' => 21846100,
			//			'46' => 23946100,
			//			'47' => 26146100,
			//			'48' => 28446100,
			//			'49' => 30846100,
			//			'50' => 33346100,
		),
		'charm_level'    => array(
			'1'  => 0,
			'2'  => 5000,
			'3'  => 15000,
			'4'  => 35000,
			'5'  => 65000,
			'6'  => 105000,
			'7'  => 155000,
			'8'  => 215000,
			'9'  => 295000,
			'10' => 395000,
			'11' => 545000,
			'12' => 745000,
			'13' => 995000,
			'14' => 1295000,
			'15' => 1645000,
			'16' => 2045000,
			'17' => 2545000,
			'18' => 3145000,
			'19' => 3845000,
			'20' => 4645000,
			'21' => 5545000,
			'22' => 6545000,
			'23' => 8545000,
			'24' => 11545000,
			'25' => 15545000,
			'26' => 20545000,
			'27' => 26545000,
			'28' => 33545000,
			'29' => 41545000,
			'30' => 50545000,
			'31' => 60545000,
			'32' => 80545000,
			'33' => 110545000,
			'34' => 150545000,
			'35' => 200545000,
			'36' => 260545000,
			'37' => 330545000,
			'38' => 410545000,
			'39' => 500545000,
			'40' => 600545000,
			'41' => 750545000,
			'42' => 950545000,
			'43' => 1200545000,
			'44' => 1500545000,
			'45' => 1850545000,
			'46' => 2250545000,
			'47' => 2700545000,
			'48' => 3200545000,
			'49' => 3750545000,
			'50' => 4350545000,
		),
		'intimate_level' => array(
			'1'  => 0,
			'2'  => 1500,
			'3'  => 3500,
			'4'  => 6000,
			'5'  => 11000,
			'6'  => 18500,
			'7'  => 28500,
			'8'  => 41000,
			'9'  => 56000,
			'10' => 76000,
			'11' => 101000,
			'12' => 131000,
			'13' => 166000,
			'14' => 206000,
			'15' => 251000,
			'16' => 301000,
			'17' => 401000,
			'18' => 551000,
			'19' => 751000,
			'20' => 1001000,
			'21' => 1401000,
			'22' => 1901000,
			'23' => 2651000,
			'24' => 3651000,
			'25' => 4901000,
			'26' => 6401000,
			'27' => 8401000,
			'28' => 10901000,
			'29' => 14901000,
			'30' => 19901000,
			//			'31' => 29901000,
			//			'32' => 44901000,
			//			'33' => 64901000,
			//			'34' => 89901000,
			//			'35' => 139901000,
			//			'36' => 214901000,
			//			'37' => 314901000,
			//			'38' => 439901000,
			//			'39' => 589901000,
			//			'40' => 764901000,
			//			'41' => 964901000,
			//			'42' => 1189901000,
			//			'43' => 1439901000,
			//			'44' => 1739901000,
			//			'45' => 2089901000,
			//			'46' => 2489901000,
			//			'47' => 2939901000,
			//			'48' => 3439901000,
			//			'49' => 3989901000,
			//			'50' => 4589901000,
		),
		'anchor_level'   => array(
			'1'  => 0,
			'2'  => 10000,
			'3'  => 40000,
			'4'  => 90000,
			'5'  => 190000,
			'6'  => 390000,
			'7'  => 690000,
			'8'  => 1090000,
			'9'  => 1590000,
			'10' => 2390000,
			'11' => 3390000,
			'12' => 4890000,
			'13' => 6890000,
			'14' => 9390000,
			'15' => 12390000,
			'16' => 15890000,
			'17' => 19890000,
			'18' => 24390000,
			'19' => 29390000,
			'20' => 39390000,
			'21' => 54390000,
			'22' => 74390000,
			'23' => 99390000,
			'24' => 129390000,
			'25' => 164390000,
			'26' => 204390000,
			'27' => 249390000,
			'28' => 299390000,
			'29' => 354390000,
			'30' => 414390000,
			//			'31' => 484390000,
			//			'32' => 564390000,
			//			'33' => 654390000,
			//			'34' => 754390000,
			//			'35' => 864390000,
			//			'36' => 984390000,
			//			'37' => 1114390000,
			//			'38' => 1254390000,
			//			'39' => 1404390000,
			//			'40' => 1604390000,
			//			'41' => 1854390000,
			//			'42' => 2154390000,
			//			'43' => 2504390000,
			//			'44' => 2904390000,
			//			'45' => 3354390000,
			//			'46' => 3854390000,
			//			'47' => 4404390000,
			//			'48' => 5004390000,
			//			'49' => 5654390000,
			//			'50' => 6354390000,
		),
	);

	return $level[$type] ? null : array();
}

/**
 * 获取贵族级别与名称的映射
 * @param $badge_level
 * @return string
 */
function get_badge_maps($badge_level) {
	$badge_map = array('', '爵士', '男爵', '子爵', '伯爵', '侯爵', '公爵', '国王');

	return isset($badge_map[$badge_level]) ? $badge_map[$badge_level] : '未知';
}

/**
 * 获取等级区间
 * @param  $current_score
 * @param  $type
 * @return array
 */
function get_level_scope($current_score, $type) {
	$current_score    = (int) $current_score;
	$level_score_maps = get_level_maps($type);
	if (!$level_score_maps || !is_array($level_score_maps)) {
		return array();
	}
	if ($current_score >= max($level_score_maps)) {
		$max_score = max($level_score_maps);
		$max_level = max(array_keys($level_score_maps));

		return array(
			'next_score'   => $max_score,
			'next_level'   => $max_level,
			'before_score' => $max_score,
			'before_level' => $max_level,
			'level'        => $max_level,
			'score'        => $max_score, //重置用户分值
			'percent'      => '100%'
		);
	}
	foreach ($level_score_maps as $next_level => $next_score) {
		$before = max($next_level - 1, 1);
		if ($current_score < $next_score && $current_score >= $level_score_maps[$before]) {
			$return['next_score']   = $next_score;
			$return['next_level']   = $next_level;
			$return['before_score'] = $level_score_maps[$before];
			$return['before_level'] = $before;
			$return['score']        = $current_score;
			$return['level']        = $before;
			$return['percent']      = $current_score > $level_score_maps[$before] ? (round(($current_score - $level_score_maps[$before]) / ($next_score - $level_score_maps[$before]), 4) * 100) . '%' : '0%';

			return $return;
		}
	}

	return array();
}

/**
 * 用户头像图片尺寸转换
 * @param string $image_url  原图地址
 * @param string $image_size 返回尺寸(支持尺寸: 50_50,70_70,130_130,160_160)
 * @return string
 */
function convert_image_userface($image_url, $image_size) {
	if (in_array($image_size, array('50_50', '70_70', '130_130'))) {
		return str_replace('_1x1', '_' . $image_size, $image_url);
	}

	//160_160直接返回原图

	return $image_url;
}

/**
 * 物品图片尺寸转换
 * @param string $image_url  原图地址
 * @param string $image_size 返回尺寸(支持尺寸: 30_30,36_36,50_50,100_100)
 * @return string
 */
function convert_image_goods($image_url, $image_size) {
	if (in_array($image_size, array('30_30', '36_36', '50_50', '100_100'))) {
		return str_replace('_30_30', '_' . $image_size, $image_url);
	}

	return $image_url;
}

/**
 * 勋章图片尺寸转换
 * @param string $image_url  原图地址
 * @param string $image_size 返回尺寸(支持尺寸: 20_20,60_60)
 * @return string
 */
function convert_image_medal($image_url, $image_size) {
	if (in_array($image_size, array('20_20', '60_60'))) {
		return str_replace('_1x1', '_' . $image_size, $image_url);
	}

	return $image_url;
}

/**
 * 直播图片尺寸转换
 * @param string $image_url  原图地址
 * @param string $image_size 返回尺寸(支持尺寸: 180_101,160_90,700_394,16x9)
 * @return string
 */
function convert_image_roomlive($image_url = '', $image_size = '180_101') {
	if (!in_array($image_size, array('180_101', '160_90', '700_394', '16x9', '3x4'))) {
		return FALSE;
	}
	//700_394等于16x9
	if ($image_size == '700_394') {
		$image_size = '16x9';
	}
	if (empty($image_url)) {
		switch ($image_size) {
			case '160_90':
				$image_url = 'http://www.qiyipic.com/ppsshow/fix/live_default_160_90.jpg';
				break;
			case '180_101':
				$image_url = 'http://www.qiyipic.com/ppsshow/fix/live_default_180_101.jpg';
				break;
			case '3x4':
				$image_url = 'http://www.qiyipic.com/ppsshow/fix/live_default_3x4.jpg';
				break;
			case '700_394':
			case '16x9':
				$image_url = 'http://www.qiyipic.com/ppsshow/fix/live_default_16x9.jpg';
				break;
		}
	} else {
		if (strpos($image_url, '_16x9') !== FALSE) {
			$image_url = str_replace('_16x9', '_' . $image_size, $image_url);
		} else {
			$image_url = preg_replace('/_(\d+)_(\d+)/', '_' . $image_size, $image_url);
		}
	}

	return $image_url;
}

/**
 * 截取中文字符， 1个中文算两个字符，1个英文算1个字符
 * @param        $string
 * @param int    $limit
 * @param string $encode
 * @param bool   $with_tips
 * @return string
 */
function cn_string_split($string, $limit = 12, $encode = 'UTF-8', $with_tips = TRUE) {
	return mb_strimwidth($string, 0, $limit, $with_tips ? '...' : '', $encode);
}

/**
 * 图片转换
 * @param       $image_url
 * @param array $size
 * @param null  $image_extension 'png', 'gif'
 * @return array
 */
function convert_image_product($image_url, $size = array(20, 24, 30, 50, 100), $image_extension = NULL) {
	//20, 30, 32, 36, 50, 60, 100
	//http://www.qiyipic.com/ppsxiu/fix/sc/wp_gz_bojue_20x20.png
	$list      = array();
	$extension = pathinfo($image_url, PATHINFO_EXTENSION);

	//图片后缀判断
	if (is_null($image_extension) || !in_array($image_extension, array('png', 'gif'))) {
		$image_extension = $extension;
	}
	$image_extension = strtolower($image_extension);

	if (strpos($image_url, 'http://www.qiyipic.com/ppsxiu/fix/sc/') !== 0) {
		$regex = "/(\\d{2,4}_\\d{2,4})" . preg_quote('.' . $extension) . '$/';
		$match = array();
		preg_match($regex, $image_url, $match);
		foreach ($size as $tmp) {
			$list[$tmp . '*' . $tmp] = $match ? str_replace($match[0], $tmp . '_' . $tmp . '.' . $image_extension, $image_url) : '';
		}

		return $list;
	} else {
		$regex = "/(\\d{2,4}x\\d{2,4})" . preg_quote('.' . $extension) . '$/';
		$match = array();
		preg_match($regex, $image_url, $match);
		foreach ($size as $tmp) {
			$list[$tmp . '*' . $tmp] = $match ? str_replace($match[0], $tmp . 'x' . $tmp . '.' . $image_extension, $image_url) : '';
		}

		return $list;
	}
}

function convert_swf_product($image_url) {
	$image_url_arr = convert_image_product($image_url, array(50));
	$image_url     = $image_url_arr['50*50'];
	//http://www.qiyipic.com/ppsxiu/fix/sc/wp_gz_bojue_20x20.png
	//http://www.iqiyi.com/player/common/sc/wp_lw_bangbangtang_50x50.swf
	$extension = pathinfo($image_url, PATHINFO_EXTENSION);
	if (strpos($image_url, 'http://www.qiyipic.com/ppsxiu/fix/sc/') !== 0) {
		return FALSE;
	} else {
		$regex_start = "#^" . preg_quote('http://www.qiyipic.com/ppsxiu/fix/sc/') . '#';
		$regex_end   = "/" . preg_quote('.' . $extension) . '$/';
		$swf_url     = preg_replace($regex_start, 'http://www.iqiyi.com/player/common/sc/', $image_url);
		$swf_url     = preg_replace($regex_end, '.swf', $swf_url);

		return $swf_url;
	}
}

/**
 * 访问记录日期格式
 * 今天/昨天/前天，最近3天内的访问；月-日，3天以前今年内访问；年-月-日；一年前的访问
 * @param $date
 * @return bool|string
 */
function format_visit_date($date) {
	$timestamp = strtotime($date);
	if ($timestamp > strtotime(date('Y-m-d'))) {
		$date = '今天';
	} else if ($timestamp > strtotime(date('Y-m-d', strtotime('-1 days')))) {
		$date = '昨天';
	} else if ($timestamp > strtotime(date('Y-m-d', strtotime('-2 days')))) {
		$date = '前天';
	} else if ($timestamp > strtotime(date('Y-01-01'))) {
		$date = date('m-d', $timestamp);
	} else {
		$date = date('Y-m-d', $timestamp);
	}

	return $date;
}

//返回数组中指定的一列
function array_col($array, $column_key, $index_key = NULL) {
	$values = array();
	if (empty($array) || !is_array($array)) {
		return $values;
	}
	if ($index_key == NULL) {
		foreach ($array as $v) {
			if (isset($v[$column_key])) {
				$values[] = $v[$column_key];
			}
		}
	} else {
		foreach ($array as $v) {
			if (isset($v[$index_key]) && isset($v[$column_key])) {
				$values[$v[$index_key]] = $v[$column_key];
			}
		}
	}

	return $values;
}

//二维数组转换:某一列做key
function array_col2key($array, $column_key) {
	$values = array();
	if (empty($array) || !is_array($array)) {
		return $values;
	}
	foreach ($array as $v) {
		if (isset($v[$column_key])) {
			$values[$v[$column_key]] = $v;
		}
	}

	return $values;
}

/**
 * 数组json解析
 * @param $array
 * @return mixed
 */
function array_json_decode($array) {
	foreach ($array as $key => $data) {
		$array[$key] = json_decode($data, TRUE);
	}

	return $array;
}

/**
 * 秀场金钱显示格式化
 * @param $number
 * @return string
 */
function format_show_money($number) {
	return number_format((float) round($number), 0, '.', ',');
}

/**
 * 按照二维数组key的值排序
 * @param     $arr
 * @param     $col
 * @param int $dir
 * @return void
 */
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	$sort_col = array();
	foreach ($arr as $key => $row) {
		$sort_col[$key] = $row[$col];
	}

	array_multisort($sort_col, $dir, $arr);
}

/**
 * 根据IP获取地址(纯真IP库)
 * @param     $ip_or_ipnum
 * @return string
 */
function convert_ip_address($ip_or_ipnum) {
	$ipnum = 0;
	if (is_numeric($ip_or_ipnum) && $ip_or_ipnum > 0) {
		$ipnum = (int) $ip_or_ipnum;
	} else {
		$ipnum = ip2long($ip_or_ipnum);
	}
	if ($ipnum < 0) {
		$ipnum += 4294967296;
	}
	if ($ipnum > 0) {
		$ret    = RedisFactory::getInstance('cache_stat')->zRangeByScore('convert_ip_address_cz', $ipnum, 4294967295, array(
			'limit' => array(
				0,
				1
			)
		));
		$ret[0] = explode('$', $ret[0]);

		return array_shift($ret[0]);
	} else {
		return NULL;
	}
}

/**
 * 验证合法的奇艺用户
 * @param $authcookie
 * @return array
 */
function getUserinfoFromQiyi($authcookie) {
	$ret = http_post_contents("http://jy.passport.qiyi.domain/apis/user/info.action", array('authcookie' => $authcookie));
	$ret = json_decode($ret, TRUE);

	return $ret && $ret['code'] == 'A00000' ? $ret['data'] : array();
}

/**
 * 获取合法用户id
 * @param $authcookie
 * @return int
 */
function getPassportIdFromQiyi($authcookie) {
	$userinfo = getUserinfoFromQiyi($authcookie);

	return $userinfo && $userinfo['userinfo'] ? $userinfo['userinfo']['uid'] : 0;
}

/**
 * 获得客户端类型
 * @return string
 */
function getClientType() {
	if (strpos(strtolower($_SERVER['HTTP_HOST']), 'pc') !== FALSE) {
		return 'pc';
	} else {
		return 'web';
	}
}

/**
 * 发送邮件
 * @param $to
 * @param $subject
 * @param $msg
 * @param $attachment
 * @return bool
 * charset: utf-8 content-type: text/html
 * example xiu_mail('user1@mail.com,user2@mail.com', 'test', 'content', './report1.xls, ./report2.xls');
 */
function xiu_mail($to, $subject, $msg, $attachment = '') {
	Core::loadLibrary('smtp');
	$smtpClient = new SMTPClient();
	$smtpClient->setServer("10.11.50.63", "465");
	$smtpClient->setSender("tp_xiu_stat@qiyi.com", "tp_xiu_stat", "pPs.com7890");
	$smtpClient->setMail($to, $subject, $msg);

	if ($attachment !== '') {
		$attachment = explode(',', $attachment);
		$attachment = array_map('trim', $attachment);
		foreach ($attachment as $file) {
			$smtpClient->attachFile(basename($file), file_get_contents($file));
		}
	}

	return $smtpClient->sendMail();
}

/**
 * 获取主播等级图标
 * @param $level
 * @return string
 */
function get_anchor_level_icon($level) {
	$level = $level > 0 ? $level : 1;
	$level = min((int) $level, 30);

	return 'http://www.qiyipic.com/ppsxiu/fix/sc/lv_zb_v' . $level . '.png';
}

/**
 * @desc   格式化直播时间
 * @author 邬磊 mailto:andywu@qiyi.com
 * @since  2015-01-07
 * @throws 注意:无DB异常处理
 * @param  string $live_time 上次直播时间
 * @param  string $format    时间格式
 * @return string
 */
function formatLiveTime($live_time, $format = 'Y-m-d H:i:s') {
	//上次直播时间不存在默认显示新晋主播
	//DateTime::createFromFormat (php5.3以上)
	if (DateTime::createFromFormat($format, $live_time) != FALSE) {
		//上次直播时间
		list($date, $time) = explode(' ', $live_time);
		$diff = floor((time() - strtotime($date)) / 86400);
		if (date('Y-m-d') != $date && $diff <= 3) {
			$ret_time = '上次直播：' . $diff . '天前';
		} elseif ($diff > 3) {
			$ret_time = '上次直播：' . $date;
		} else {
			$ret_time = '上次直播：' . date('H:i', strtotime($live_time));
		}
	} else {
		$ret_time = '新晋主播';
	}

	return $ret_time;
}