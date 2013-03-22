<?php

	namespace x7;
	
	$ses->check_bans();
	
	$mail = $x7->mail();
	$users = $x7->users();
	
	$email = isset($_POST['email']) ? $_POST['email'] : null;
	
	try
	{
		$user = $users->load_by_email($email);
		
		// Check if account is a guest
		if(empty($user->password))
		{
			throw new exception\nonexistent_user_email;
		}
		
		$token = sha1(microtime() . print_r($_SERVER, 1) . crypt(microtime() . mt_rand(0, mt_getrandmax())));
		$user->reset_password = $token;
		$users->save_user($user, array('reset_password'));
		
		$mail->send($user->email, 'reset_password', array(
			'reset_url' => $x7->url('updatepassword?token=' . $token),
		));
		
		$ses->set_message($x7->lang('reset_token_sent'), 'notice');
		$req->go('login');
	}
	catch(exception\nonexistent_user_email $ex)
	{
		$ses->set_message($x7->lang('email_not_registered'));
		$req->go('resetpassword', true);
	}