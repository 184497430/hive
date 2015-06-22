<?php

//路径
define('APP_ROOT', dirname(dirname(dirname(__FILE__))) . '/app/admin');

define('PAGE_404', dirname(__FILE__) . '/404.php');

define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_ACTION', 'index');

//加载框架
require_once(dirname(dirname(dirname(__FILE__))) . '/core/runtime.php');


/**
 * 定义rewrite
 */
function rewriteHook(){

    $interface = array_filter( explode('.', $_GET['interface']) );

    if( !is_array($interface) || count($interface) != 2 ) return;

    $_GET['controller'] = $interface[0];
    $_GET['action'] = $interface[1];

}

function staticRes($file){
    if(!empty($file)){
        return Core::getConfig('static') . "/{$file}";
    }else{
        return "";
    }
}

//Core::addRewriteHook("rewriteHook");
Core::run();