<?php
/**
 *  model的基础类文件
 * @file      :   model.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-16
 */
abstract class Model
{

    protected static function getClass(){}

    //单例模式
    public static function getInstance() {
        static $_instance = NULL;
        if (is_null($_instance)) {
            $class = get_called_class();
            $_instance = new $class();
        }

        return $_instance;
    }

	public function __construct() {

	}
}