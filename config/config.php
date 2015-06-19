<?php
/**
 * 公共配置文件
 * @file      :   config.php
 * @copyright Copyright 2014 Qiyi Inc. All rights reserved.
 * @author    : zhangy <zhangy@qiyi.com>
 * @date      :   14-5-22
 */

return array(
	'debug'                          => FALSE,
	//数据库配置
	'db'                             => array(
		//秀场业务-主库(proxy)
		'dada'         => array(
			'dbtype' => 'mysql',
			'dbuser' => 'zhangyuan82',
			'dbpswd' => 'zhang__821016',
			'dbname' => 'zhangyuan82',
			'dbhost' => 'localhost',
			'dbport' => '3306'
		)
	),
	'redis'                          => array(

	),

    'static'    => 'http://' . $_SERVER['HTTP_HOST'] . "/public/api/static",
);