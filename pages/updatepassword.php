<?php

	namespace x7;
	
	$ses->check_bans();
	
	if($x7->system_config('disable_account_management'))
	{
		$ses->set_message($x7->lang('feature_disabled'));
		$req->go('login');
	}
	
	$users = $x7->users();
	$auth = $x7->auth();
	
	$token = isset($_GET['token']) ? $_GET['token'] : null;
	
	try
	{
		$user = $users->load_by_password_token($token);
	}
	catch(exception\nonexistent_user_password_token $ex)
	{
		$ses->set_message($x7->lang('invalid_reset_token'));
		$req->go('resetpassword');
	}
	
	$pass = substr(sha1(microtime() . print_r($_SERVER, 1) . crypt(microtime() . mt_rand(0, mt_getrandmax()))), 0, 8);
	$user->password = $auth->hash_password($pass);
	$user->reset_password = '';
	$users->save_user($user, array('password', 'reset_password'));
	
	$ses->set_message($x7->lang('password_updated', array(
		':password' => $pass,
	)), 'notice');
	$req->go('login');