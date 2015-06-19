<?php
/**
 * 测试环境下的配置文件
 * 此环境下配置项将继承于config.php，并在此文件中对某些配置项进行重写或补充
 * @file      :   config_test.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-22
 */
return array(
    'debug'                          => FALSE,
    //数据库配置
    'db'                             => array(
        //秀场业务-主库(proxy)
        'show'         => array(
            'dbtype' => 'mysql',
            'dbuser' => 'showdb_new',
            'dbpswd' => 'knohwjdx',
            'dbname' => 'showdb',
            'dbhost' => 'jy.liveshowproxy.w.qiyi.db',
            'dbport' => '8424'
        )
    ),
    'redis'                          => array(

    ),
);