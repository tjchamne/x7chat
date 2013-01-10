<?php

	date_default_timezone_set('UTC');

	$config = require('./config.php');
	if(!is_array($config))
	{
		header('Location: install.php');
		die("Redirecting to install.php");
	}
	
	require('./includes/x7chat.php');
	$x7 = new x7chat;

	$page = isset($_GET['page']) ? $_GET['page'] : 'chat';
	if(preg_match('#[^a-z0-9_]#', $page) || !file_exists('./pages/' . $page . '.php')) {
		throw new exception('Invalid page');
	}
	
	if(!in_array($page, array('sync', 'login', 'dologin')))
	{
		$x7->load('user');
		try
		{
			$user = new x7_user();
			$user_banned = $user->banned();
		}
		catch(x7_exception $ex)
		{
			$user_banned = false;
			
			if($_SESSION['user_id'])
			{
				$_SESSION = array();
				session_destroy();
				$x7->go('login');
			}
		}
		
		if(x7_check_ip_bans() || $user_banned)
		{
			$x7->set_message($x7->lang('login_failed_banned'));
			$x7->go('login');
		}
	}
	
	require('./pages/' . $page . '.php');