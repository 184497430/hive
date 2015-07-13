<?php
/**
 * 控制器基础类文件
 * @file      :   controller.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-16
 */
class Controller
{

	/**
	 * 当前控制名
	 * @var string
	 */
	public $controller;

	/**
	 * 当前action名
	 * @var string
	 */
	public $action;

    public $tpl_vars = array();

	public function __construct() {

	}

    public function _request($key, $default_val = null){
        return isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : $default_val;
    }

    public function _post($key, $default_val = null){
        return isset( $_POST[$key] ) ? $_POST[$key] : $default_val;
    }

    public function _get($key, $default_val = null){
        return isset( $_GET[$key] ) ? $_GET[$key] : $default_val;
    }

    public function assign($var, $value){
        $this->tpl_vars[$var] = $value;

    }

	/**
	 * 渲染模版
	 * @param string $name 模版名
	 * @param array  $vars 模版变量
	 * @return void
	 */
	public function show($name, array $vars = NULL) {

		$file = APP_ROOT.DS.'view'.DS.strtolower($name).'.view.php';
		if (!empty($vars) && is_array($vars)) {
            $this->tpl_vars = array_merge($this->tpl_vars, $vars);
		}

        if (!empty($this->tpl_vars)) {
            extract($this->tpl_vars);
        }

		require($file);
	}

	/**
	 * 获取渲染结果
	 * @param string $name
	 * @param array  $vars
	 * @return string
	 */
	public function getView($name, array $vars = NULL) {
		ob_start();

		$this->show($name, $vars);
		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	/**
	 * 以json格式输出
	 *
	 * @param string $code
	 * @param string $msg
	 * @param string $data
	 * @return void
	 */
	public function showJsonResult($code='', $msg='', $data=''){
		echo $this->getJsonResult($code, $msg, $data);
	}

	public function formatResult($code='', $msg='', $data=''){
		$result = array(
			'code'	=> $code,
			'msg'	=> $msg,
			'data'	=> $data
		);

		return $result;
	}

	/**
	 * 获得json格式的结果
	 *
	 * @param string $code
	 * @param string $msg
	 * @param string $data
	 * @return string
	 */
	public function getJsonResult($code='', $msg='', $data=''){
		$result = array(
			'code'	=> $code,
			'msg'	=> $msg,
			'data'	=> $data
		);
		return json_encode($result);
	}
}