<?php

	namespace x7;
	
	$ses->check_bans();
	
	if($x7->system_config('disable_account_management'))
	{
		$ses->set_message($x7->lang('feature_disabled'));
		$req->go('login');
	}
	
	$auth = $x7->auth();

	$fail = false;
	
	$username = isset($_POST['username']) ? $_POST['username'] : null;
	if(!$username)
	{
		$ses->set_message($x7->lang('missing_register_username'));
		$fail = true;
	}
	
	$password = isset($_POST['password']) ? $_POST['password'] : null;
	if(!$password)
	{
		$ses->set_message($x7->lang('missing_register_password'));
		$fail = true;
	}
	
	$repassword = isset($_POST['repassword']) ? $_POST['repassword'] : null;
	if($password !== $repassword)
	{
		$ses->set_message($x7->lang('passwords_donot_match'));
		$fail = true;
	}
	
	$email = isset($_POST['email']) ? $_POST['email'] : null;
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$ses->set_message($x7->lang('invalid_email'));
		$fail = true;
	}
	
	$sql = "SELECT * FROM {$x7->dbprefix}users WHERE username = :username OR email = :email";
	$st = $db->prepare($sql);
	$st->execute(array(':username' => $username, ':email' => $email));
	while($check = $st->fetch())
	{
		$fail = true;
			
		if($username && $check['username'] === $username)
		{
			$ses->set_message($x7->lang('username_in_use'));
		}
		elseif(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$ses->set_message($x7->lang('email_in_use'));
		}
	}
	
	if($fail)
	{
		$req->go('register', true);
	}
	
	
	$hashed_password = $auth->hash_password($password);
	
	$sql = "INSERT INTO {$x7->dbprefix}users (username, password, email) VALUES (:username, :password, :email)";
	$st = $db->prepare($sql);
	$st->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));
	$user_id = $db->lastInsertId();
	
	$req->go('chat');