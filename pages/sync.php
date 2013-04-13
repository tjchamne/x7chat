<?php

	namespace x7;
	
	$req->is_ajax_request(true);
	
	$user = $ses->current_user();
	
	$users = $x7->users();
	
	$user_id = $user->id;
	
	$local_sync_time = 11;
	$global_sync_time = 31;
	$user_expiration_time = 61;

	$last_event_id = isset($_SESSION['last_event_id']) ? $_SESSION['last_event_id'] : 0;
	$orig_last_event_id = $last_event_id;
	
	$last_global_sync_time = isset($_SESSION['last_global_sync_time']) ? $_SESSION['last_global_sync_time'] : 0;
	$last_local_sync_time = isset($_SESSION['last_local_sync_time']) ? $_SESSION['last_local_sync_time'] : 0;
	$server_rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
	
	$process_rooms = $server_rooms;
	$process_rooms[] = 0;
	$rooms = implode(',', $process_rooms);
	
	if(empty($last_event_id))
	{
		$sql = "
			SELECT
				MAX(id) as id
			FROM {$x7->dbprefix}messages
		";
		$st = $db->prepare($sql);
		$st->execute();
		$row = $st->fetch();
		$st->closeCursor();
		if($row)
		{
			$last_event_id = $row['id'];
			$_SESSION['last_event_id'] = $row['id'];
		}
	}
	
	// refresh the user's last update time
	if($last_local_sync_time < time() - $local_sync_time)
	{
		$_SESSION['last_local_sync_time'] = time();
		
		$sql = "
			UPDATE {$x7->dbprefix}users
			SET
				timestamp = :timestamp,
				ip = :ip
			WHERE
				id = :user_id
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':user_id' => $user_id,
			':timestamp' => date('Y-m-d H:i:s'),
			':ip' => $_SERVER['REMOTE_ADDR'],
		));
	}
	
	// cleared expired users from the online table
	if($last_global_sync_time < time() - $global_sync_time)
	{
		$_SESSION['last_global_sync_time'] = time();
		$users->timeout_users();
	}
	
	// pull new messages
	$sql = "
		SELECT
			message.*,
			user.username AS source_name
		FROM {$x7->dbprefix}messages message
		LEFT JOIN {$x7->dbprefix}users user ON
			message.source_type = 'user'
			AND user.id = message.source_id
		WHERE
			message.id > :last_event_id
			AND
			(
				(
					message.dest_type = 'room'
					AND
					message.dest_id IN ({$rooms})
				)
				OR
				(
					message.dest_type = 'user'
					AND
					message.dest_id IN (0,:user_id)
				)
			)
			AND NOT
			(
				message.source_type = 'user'
				AND
				message.source_id = :user_id
			)
	";
	$st = $db->prepare($sql);
	$st->execute(array(':user_id' => $user_id, ':last_event_id' => $last_event_id));
	$events = $st->fetchAll();
	
	$output = array();
	
	$do_resync = false;
	$filter_resync = false;
	$smiley_resync = false;
	foreach($events as $key => $event)
	{
		$events[$key]['timestamp'] = strtotime($event['timestamp']);
	
	
		if($event['id'] > $_SESSION['last_event_id'])
		{
			$_SESSION['last_event_id'] = $event['id'];
		}
		
		if($event['message_type'] == 'room_resync')
		{
			$_SESSION['last_global_sync_time'] = time();
			$do_resync = true;
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'filter_resync')
		{
			$filter_resync = true;
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'smiley_resync')
		{
			$smiley_resync = true;
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'ban_resync')
		{
			$ses->check_bans();
			unset($events[$key]);
		}
		
		if($event['message_type'] == 'logout')
		{
			die(json_encode(array('redirect' => $x7->url('logout'))));
		}
	}
	
	$output['events'] = $events;
	
	if($filter_resync)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}word_filters
			ORDER BY
				LENGTH(word) DESC
		";
		$st = $db->prepare($sql);
		$st->execute();
		$filters = $st->fetchAll();
		$output['filters'] = $filters;
	}
	
	if($smiley_resync)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}smilies
			ORDER BY
				LENGTH(token) DESC
		";
		$st = $db->prepare($sql);
		$st->execute();
		$smilies = $st->fetchAll();
		$output['smilies'] = $smilies;
	}
	
	if($do_resync)
	{
		$sql = "
			SELECT
				room_user.*,
				user.username AS user_name,
				user.status_type AS user_status,
				`group`.color,
				`group`.image
			FROM {$x7->dbprefix}room_users room_user
			INNER JOIN {$x7->dbprefix}users user ON
				user.id = room_user.user_id
			LEFT JOIN {$x7->dbprefix}groups `group` ON
				group.id = user.group_id
			WHERE
				room_id IN ({$rooms})
		";
		$st = $db->prepare($sql);
		$st->execute();
		$users = $st->fetchAll();
		$output['users'] = $users;
	}
	
	echo json_encode($output);