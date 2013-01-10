<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}users
		WHERE
			id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		'user_id' => $user_id,
	));
	$user = $st->fetch();
	
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$real_name = isset($_POST['real_name']) ? $_POST['real_name'] : '';
	$bio = isset($_POST['bio']) ? $_POST['bio'] : '';
	$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
	$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
	$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
	$retype_new_password = isset($_POST['retype_new_password']) ? $_POST['retype_new_password'] : '';
	
	$data = array(
		':user_id' => $user_id,
		':real_name' => $real_name, 
		':bio' => $bio, 
		':gender' => $gender
	);
	
	$fields = '';
	
	$priv_change = false;
	$fail = false;

	require('./includes/libraries/phpass/PasswordHash.php');
	$phpass = new PasswordHash(8, false);
	
	if($user['email'] != $email)
	{
		$priv_change = true;
		$data[':email'] = $email;
		$fields .= ',email = :email';
	}
	
	if($new_password || $retype_new_password)
	{
		if($new_password != $retype_new_password)
		{
			$fail = true;
			$x7->set_message($x7->lang('passwords_donot_match'));
		}
		
		$priv_change = true;
		$hashed_password = $phpass->HashPassword($new_password);
		$data[':password'] = $hashed_password;
		$fields .= ',password = :password';
	}
	
	if($priv_change)
	{
		if(!$current_password || !$user['password'] || !$phpass->CheckPassword($current_password, $user['password']))
		{
			$fail = true;
			$x7->set_message($x7->lang('current_password_wrong'));
		}
	}
	
	if(!$fail)
	{
		$sql = "
			UPDATE {$x7->dbprefix}users SET
				real_name = :real_name,
				about = :bio,
				gender = :gender
				{$fields}
			WHERE
				id = :user_id
		";
		$st = $db->prepare($sql);
		$st->execute($data);
		
		$x7->set_message($x7->lang('settings_updated'), 'notice');
		$x7->go('settings');
	}
	else
	{
		$x7->go('settings');
	}
	