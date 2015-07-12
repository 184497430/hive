<?php
/**
 * Created by PhpStorm.
 * User: zhangy
 * Date: 14-5-16
 * Time: 上午11:10
 */
//应用路径必须配置
defined('APP_ROOT') || die('APP_ROOT not defined!');
//路径分隔符
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
//框架根目录
defined('CORE_ROOT') || define('CORE_ROOT', dirname(__FILE__));
//助手目录
defined('HELPER_ROOT') || define('HELPER_ROOT', dirname(CORE_ROOT) . '/helper');
//配置文件目录
defined('CONFIG_ROOT') || define('CONFIG_ROOT', dirname(CORE_ROOT) . '/config');

//运行模式
if (file_exists(CONFIG_ROOT . DS . 'app_env.ini') && is_readable(CONFIG_ROOT . DS . 'app_env.ini')) {
	defined('APP_ENV') || define('APP_ENV', file_get_contents(CONFIG_ROOT . DS . 'app_env.ini'));
} else {
	defined('APP_ENV') || define('APP_ENV', 'production');
}

date_default_timezone_set ('Asia/Shanghai');

require(CORE_ROOT . DS . 'controller.php');
require(CORE_ROOT . DS . 'model.php');
require(CORE_ROOT . DS . 'util.php');


class Core
{

    private static $rewrite_hook;

	public static function run() {

		//注册类自动加载方法
		spl_autoload_register(array('Core', '_autoload'));

		switch (APP_ENV) {
			case 'development':
				ini_set('display_errors', TRUE);
				error_reporting(E_ALL ^ E_NOTICE);
				if ($_GET['xhprof'] && function_exists('xhprof_enable')) {
					xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
					register_shutdown_function(array('Core', '_cHandlerXhprofSave'));
				}
				break;
			case 'test':
			case 'production':
			default:
				error_reporting(0);
				break;
		}

        if( !empty(self::$rewrite_hook) ){
            foreach(self::$rewrite_hook as $fun){
                $fun();
            }
        }

        self::router();
	}

	public static function _cHandlerXhprofSave() {
		$xhprof_data = xhprof_disable();
		include_once CORE_ROOT . DS . "lib/xhprof/utils/xhprof_lib.php";
		include_once CORE_ROOT . DS . "lib/xhprof/utils/xhprof_runs.php";

		$xhprof_runs = new XHProfRuns_Default();
		$run_id      = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
		echo "<br><a href=\"http://www.xhprof.domain/index.php?run={$run_id}&source=xhprof_foo\" target=\"_blank\">xhprof</a>";
	}

	/*
	 * 加载配置文件
	 * @config_name 配置项，支持通过“.”来获取具体某项值。如“db.show.type”
	 * @return 返回具体配置值
	 */
	public static function getConfig($config_name) {

		static $C;

		if (empty($config_name)) {
			return NULL;
		}

		if (empty($C)) {

			switch (APP_ENV) {
				case 'development':
					$C = include(CONFIG_ROOT . DS . 'config_dev.php');
					break;
				case 'test':
					$C = include(CONFIG_ROOT . DS . 'config_test.php');
					break;
				case 'production':
				default:
					$C = include(CONFIG_ROOT . DS . 'config.php');
					break;
			}
		}

		$config_name = array_filter(explode('.', $config_name));

		$result = $C;
		foreach ($config_name as $each) {
			if (isset($result[$each])) {
				$result = $result[$each];
			} else {
				return NULL;
			}
		}

		return $result;
	}

	/**
	 * 自动加载文件
	 * @param $class_name
	 */
	public static function _autoload($class_name) {
		$dir_root   = APP_ROOT;
		$class_name = strtolower(str_replace('Service\\', '', $class_name));

		if (substr($class_name, -10) == 'controller') {
			$file_name = substr($class_name, 0, -10) . '.ctl.php';
			require($dir_root . DS . 'controller' . DS . $file_name);
		} elseif (substr($class_name, -5) == 'model') {
			$file_name = substr($class_name, 0, -5) . '.mod.php';
			require($dir_root . DS . 'model' . DS . $file_name);
		} elseif (substr($class_name, -6) == 'helper') {
			$file_name = substr($class_name, 0, -6) . '.hlp.php';
			require(HELPER_ROOT . DS . $file_name);
		} elseif (substr($class_name, -2) == 'db') {
			$file_name = substr($class_name, 0, -2) . '.db.php';
			require($dir_root . DS . 'model' . DS . 'db' . DS . $file_name);
		} elseif (substr($class_name, -5) == 'redis') {
			$file_name = substr($class_name, 0, -5) . '.redis.php';
			require($dir_root . DS . 'model' . DS . 'redis' . DS . $file_name);
		} elseif ($class_name == 'sqlite' || $class_name == 'table') {
			require(CORE_ROOT . DS . 'db.php');
		} elseif ($class_name == 'redisfactory') {
			require(CORE_ROOT . DS . 'redis.php');
		} elseif ($class_name == 'mqfactory') {
			require(CORE_ROOT . DS . 'mq.php');
		}
	}

	/**
	 * 加载类库文件
	 * @param $lib_name 类库名
	 */
	public static function loadLibrary($lib_name) {
		include_once(CORE_ROOT . DS . 'lib' . DS . strtolower($lib_name) . '.lib.php');
	}

	/**
	 * 获取单一对象
	 * @param $className 类名
	 * @return mixed   返回一个对象
	 */
	public static function getInstance($class_name) {
		static $_cache = array();
		if (!is_object($_cache[$class_name])) {
			$_cache[$class_name] = new $class_name;
		}

		return $_cache[$class_name];
	}

	/**
	 * 调用路由文件
	 */
	public static function router() {

		$controller   = $_GET['controller'] ? $_GET['controller'] : DEFAULT_CONTROLLER;
		$action       = $_GET['action'] ? $_GET['action'] : DEFAULT_ACTION;

        $controller = str_replace( array('/', '\\' , '.', '*', '?'), array('','', '','',''), $controller );
		$controller_file = APP_ROOT . DS . 'controller' . DS . $controller . '.ctl.php';
		if (file_exists($controller_file) && is_readable($controller_file)) {
			$controller_class_name = $controller . 'Controller';
			$action                = $action . "Action";
			$controller            = new $controller_class_name;
			if ( is_callable(array($controller, $action)) ) {
				call_user_func(array($controller, $action));

				return;
			}
		}

        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");

        if( defined('PAGE_404') ){
            include PAGE_404;
        }

        exit;
	}

    public static function addRewriteHook($fun){

        if( function_exists( $fun ) ){
            self::$rewrite_hook[] = $fun;
        }else{
            trigger_error("缺少函数{$fun}", E_USER_ERROR);
        }

    }
}
