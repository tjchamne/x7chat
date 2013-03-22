<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	
	$edit_user_data = array();
	if($user_id)
	{
		try
		{
			$edit_user = new x7_user($user_id);
			$edit_user_data = $edit_user->data();
		}
		catch(x7_exception $ex)
		{
			$ses->set_message($x7->lang('user_not_found'));
			$req->go('admin_users');
		}
	}
	
	if(!$edit_user_data && $user_id)
	{
		$ses->set_message($x7->lang('user_not_found'));
		$req->go('admin_list_users');
	}
	
	$fail = false;
	
	// Is username being changed or set
	if(!$user_id || $edit_user_data['username'] != $username)
	{
		try
		{
			$check_user = new x7_user($username, 'username');
			$check_user->data();
			$fail = true;
			$ses->set_message($x7->lang('username_in_use'));
		}
		catch(x7_exception $ex)
		{
		}
	}
	
	// Is email being changed or set
	if(!$user_id || $edit_user_data['email'] != $email)
	{
		try
		{
			$check_user = new x7_user($email, 'email');
			$check_user->data();
			$fail = true;
			$ses->set_message($x7->lang('email_in_use'));
		}
		catch(x7_exception $ex)
		{
		}
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$fail = true;
			$ses->set_message($x7->lang('invalid_email'));
		}
	}
	
	if(!$user_id && !$password)
	{
		$fail = true;
		$ses->set_message($x7->lang('missing_register_password'));
	}
	
	if(empty($fail))
	{
		if($password)
		{
			require('./includes/libraries/phpass/PasswordHash.php');
			$phpass = new PasswordHash(8, false);
			$password = $phpass->HashPassword($password);
		}
		else
		{
			$password = $edit_user_data['password'];
		}
		
		$params = array(
			':username' => $username,
			':email' => $email,
			':password' => $password,
		);
		
		if($user_id)
		{
			$sql = "
				UPDATE {$x7->dbprefix}users SET
					username = :username,
					email = :email,
					password = :password
				WHERE
					id = :user_id
			";
			$params[':user_id'] = $user_id;
		}
		else
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}users SET
					username = :username,
					email = :email,
					password = :password
			";
		}
		
		$st = $db->prepare($sql);
		$st->execute($params);
		
		$ses->set_message($x7->lang('user_updated'), 'notice');
		$req->go('admin_list_users');
	}
	else
	{
		$req->go('admin_edit_user?id=' . $user_id, true);
	}