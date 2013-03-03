<?php
	$x7->load('user');
	$db = $x7->db();
	
	$token = isset($_GET['token']) ? $_GET['token'] : null;
	
	try
	{
		$user = new x7_user($token, 'reset_password');
		$user->data();
	}
	catch(x7_exception $ex)
	{
		$x7->set_message($x7->lang('invalid_reset_token'));
		$x7->go('resetpassword');
	}
	
	require('./includes/libraries/phpass/PasswordHash.php');
	$phpass = new PasswordHash(8, false);
	$pass = substr(sha1($phpass->HashPassword(mt_rand() . microtime(TRUE) . print_r($_SERVER, 1))), 0, 16);
	$hashed_pass = $phpass->HashPassword($pass);
	
	$sql = "
		UPDATE {$x7->dbprefix}users SET
			reset_password = '',
			password = :password
		WHERE
			id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':password' => $hashed_pass,
		':user_id' => $user->id(),
	));
	
	$x7->set_message($x7->lang('password_updated', array(
		':password' => $pass,
	)), 'notice');
	$x7->go('login');