<?php

/**
 * Settings to be stored in dependency injector
 */

$settings = array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'name' => 'cli',
		'port' => 3306
	),
);


return $settings;
