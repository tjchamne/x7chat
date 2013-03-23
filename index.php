<?php

	namespace x7;

	$config = require('./config.php');
	
	if(is_array($config))
	{
		if(!empty($config['auth_plugin']))
		{
			$ext_config = require('includes/integration/' . $config['auth_plugin'] . '/config_loader.php');
			$config = array_merge($config, $ext_config);
		}
		
		if(!empty($config['debug']))
		{
			error_reporting(E_ALL);
			ini_set('display_errors', 'on');
		}
		else
		{
			ini_set('display_errors', 'off');
		}
	}
	elseif(empty($config['dbname']))
	{
		header('Location: ./install/index.php');
		die("Redirecting to install/index.php");
	}
	
	require('./includes/x7chat.php');
	$x7 = new x7chat($config);

	$ses = $x7->session();
	$req = $x7->request();
	$db = $x7->db();
	
	$default_page = false;
	if(!isset($_GET['page']))
	{
		$default_page = true;
		$page = 'chat';
	}
	else
	{
		$page = $_GET['page'];
	}
	
	if(preg_match('#[^a-z0-9_]#', $page) || !file_exists('./pages/' . $page . '.php')) {
		$page = 'login';
		$x7->session()->set_message($x7->lang('page_not_found'));
	}
	
	try
	{
		if(!empty($_GET['session_key']))
		{
			$ses->handle_key($_GET['session_key']);
		}
		
		require('./pages/' . $page . '.php');
	}
	catch(exception\user_banned $ex)
	{
		$ses->set_message($x7->lang('login_failed_banned'));
		$req->go('login');
	}
	catch(exception\user_not_authenticated $ex)
	{
		if(!$default_page)
		{
			$x7->session()->set_message($x7->lang('login_required'));
		}
		
		$req->go('login');
	}