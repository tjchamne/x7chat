<?php
/*
Plugin Name: X7 Chat
Plugin URI: http://www.x7chat.com/
Description: A chatroom
Version: 3.2.0a3
Author: Tim Chamness
Author URI: http://www.x7chat.com/
License: GNU GPL3
*/

register_activation_hook(__FILE__, function() {
	ini_set('display_errors', 'on');
	error_reporting(E_ALL);
	
	$root = dirname(__FILE__) . '/';
	
	require($root . 'install/util.php');
	
	$config = require($root . 'config.php');
	$ext_config = require($root . 'includes/integration/wordpress/config_loader.php');
	$config = array_merge($config, $ext_config);
	
	try
	{
		$db = db_connection($config);
		patch_sql($db, $config['prefix']);
	}
	catch(exception $err)
	{
		die($err->getMessage());
	}
});

add_shortcode('x7chat', function() {
	$chat = plugins_url('index.php', __FILE__);
	$base = dirname(__FILE__) . '/';
	require_once($base . 'integration/wordpress.php');
	$x7 = new \x7\wordpress($base);
	$key = $x7->generate_session_key();
	return "<iframe src='{$chat}?session_key={$key}' width='100%' height='600'></iframe>";
});

add_filter('query_vars', function($vars) {
	$vars[] = 'x7chat';
	return $vars;
});

add_action('parse_request', function($wp) {
	if(isset($wp->query_vars['x7chat'])) {
		$base = dirname(__FILE__) . '/';
		require_once($base . 'integration/wordpress.php');
		$x7 = new \x7\wordpress($base);
		$x7->handle_message();
		exit;
	}
});