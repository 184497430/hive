<?php
/**
 * 开发环境下的配置文件
 * 此环境下配置项将继承于config.php，并在此文件中对某些配置项进行重写或补充
 * @see       config.php
 * @file      :   config_dev.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-22
 */

return array(
    'debug'                          => FALSE,
    //数据库配置
    'sqlite'                         => dirname(dirname(__FILE__)) . "/data/hive.db",

    'static'    => 'http://' . $_SERVER['HTTP_HOST'] . "/static",
);