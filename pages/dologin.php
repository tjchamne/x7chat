<?php

	namespace x7;
	
	$ses->check_bans();
	
	$username = isset($_POST['username']) ? $_POST['username'] : null;
	if(!$username)
	{
		$ses->set_message($x7->lang('missing_login_username'));
		$req->go('login');
	}
	
	$password = isset($_POST['password']) ? $_POST['password'] : null;
	
	try
	{
		$x7->auth()->login_user($username, $password);
		$ses->check_bans();
	}
	catch(exception\authentication_exception $ex)
	{
		$ses->set_message($x7->lang('login_failed'));
		$req->go('login', true);
	}
	
	$req->go('chat');