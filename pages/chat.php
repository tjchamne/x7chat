<?php
	$x7->load('user');
	
	$db = $x7->db();

	if(empty($_SESSION['user_id']))
	{
		$x7->go('login');
	}

	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			id,
			username
		FROM {$x7->dbprefix}users
		WHERE
			id = :user_id
		LIMIT 1
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		'user_id' => $user_id,
	));
	$user = $st->fetch();
	$st->closeCursor();
	
	$user_ob = new x7_user();
	$perms = $user_ob->permissions();
	$access_acp = !empty($perms['access_admin_panel']);
	
	if(empty($_SESSION['rooms']))
	{
		$auto_join = $x7->config('auto_join');
	}
	
	$x7->display('pages/chat', array('user' => $user, 'access_acp' => $access_acp, 'auto_join' => $auto_join));