<?php
	
	global $table_prefix;
	
	if(!defined('ABSPATH'))
	{
		define('ABSPATH', dirname(__FILE__) . '/');
		require(dirname(__FILE__) . '../../../../../../../wp-config.php');
	}
	
	$ext_config = array(
		'user' => DB_USER,
		'pass' => DB_PASSWORD,
		'dbname' => DB_NAME,
		'host' => DB_HOST,
		'prefix' => $table_prefix . 'x7_',
		'api_key' => AUTH_KEY,
	);
	
	return $ext_config;