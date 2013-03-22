<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$users = $x7->users();
	
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
	$by = isset($_GET['by']) ? $_GET['by'] : 0;
	
	if(!$user_id)
	{
		throw new exception("Missing parameter value for user_id");
	}
	
	if(!in_array($by, array('ip', 'account')))
	{
		throw new exception("Invalid parameter value for by");
	}
	
	$banning_user = $users->load_by_id($user_id);
	
	if(!$banning_user->ip)
	{
		$x7->fatal_error($x7->lang('login_failed_banned_unknown_ip'));
	}
	
	if($by == 'ip')
	{
		$sql = "
			REPLACE INTO {$x7->dbprefix}bans (ip) VALUES (:ip)
		";
		$params = array(':ip' => $banning_user->ip);
	}
	else
	{
		$sql = "
			UPDATE {$x7->dbprefix}users
			SET
				banned = 1
			WHERE
				id = :user_id
		";
		$params = array(':user_id' => $user_id);
	}
	
	$st = $db->prepare($sql);
	$st->execute($params);
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id, message) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id, :message)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'ban_resync', 
		':message' => '',
		':dest_type' => 'user', 
		':dest_id' => ($by == 'account' ? $user_id : 0), 
		':source_type' => 'system', 
		':source_id' => 0,
	));
	
	$ses->set_message($x7->lang('user_banned'), 'notice');
	$req->go('user_room_profile?user=' . $user_id);