<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			username,
			real_name, 
			about, 
			gender,
			email,
			enable_sounds
		FROM {$x7->dbprefix}users
		WHERE
			id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		'user_id' => $user_id,
	));
	$user = $st->fetch();
	
	$genders = array(
		'male' => $x7->lang('male'),
		'female' => $x7->lang('female'),
	);
	
	$x7->display('pages/settings', array('genders' => $genders, 'user' => $user));