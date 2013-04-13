<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
	if(!$room_id)
	{
		$room_id = isset($_POST['room_id']) ? $_POST['room_id'] : 0;
	}
	
	$room_pass = isset($_GET['password']) ? $_GET['password'] : '';

	$server_rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}rooms room
		WHERE
			room.id = :room_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(':room_id' => $room_id));
	$room = $st->fetch();
	$st->closeCursor();
	
	if(!$room)
	{
		$x7->fatal_error($x7->lang('invalid_room'));
	}
	
	$pass = $room['password'];
	unset($room['password']);
	
	if($pass)
	{
		require('./includes/libraries/phpass/PasswordHash.php');
		$phpass = new \PasswordHash(8, false);
	
		if(empty($room_pass))
		{
			$req->go('roompass?room_id=' . $room_id);
		}
		elseif(!$phpass->CheckPassword($room_pass, $pass))
		{
			$ses->set_message($x7->lang('room_password_fail'));
			$req->go('roompass?room_id=' . $room_id);
		}
	}
	
	$_SESSION['last_local_sync_time'] = time();
	$_SESSION['last_global_sync_time'] = time();
	
	$users->timeout_users();
		
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
		':user_id' => $user->id,
		':timestamp' => date('Y-m-d H:i:s'),
		':ip' => $_SERVER['REMOTE_ADDR'],
	));
	
	$sql = "
		INSERT IGNORE INTO {$x7->dbprefix}room_users (user_id, room_id) VALUES (:user_id, :room_id)
	";
	$st = $db->prepare($sql);
	$st->execute(array(':room_id' => $room_id, ':user_id' => $user->id));
	
	$sql = "
		INSERT INTO {$x7->dbprefix}online (user_id, room_id, join_timestamp) VALUES (:user_id, :room_id, :now)
	";
	$st = $db->prepare($sql);
	$st->execute(array(':room_id' => $room_id, ':user_id' => $user->id, ':now' => date('Y-m-d H:i:s')));
	
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
			room_id = :room_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(':room_id' => $room_id));
	$users = $st->fetchAll();
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'room_resync', 
		':dest_type' => 'room', 
		':dest_id' => $room_id, 
		':source_type' => 'system', 
		':source_id' => 0,
	));
	
	if($room['greeting'])
	{
		$greet = str_replace('%u', $user->username, $room['greeting']);
		
		$sql = "
			INSERT INTO {$x7->dbprefix}messages (timestamp, message, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message, :message_type, :dest_type, :dest_id, :source_type, :source_id)
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':timestamp' => date('Y-m-d H:i:s'), 
			':message' => $greet,
			':message_type' => 'message', 
			':dest_type' => 'user', 
			':dest_id' => $user->id, 
			':source_type' => 'system', 
			':source_id' => 0,
		));
	}
	
	$sql = "
		SELECT
			message.*,
			user.username AS source_name
		FROM {$x7->dbprefix}messages message
		LEFT JOIN {$x7->dbprefix}users user ON
			message.source_type = 'user'
			AND user.id = message.source_id
		WHERE
			message.dest_type = 'room'
			AND message.dest_id = :room_id
			AND message.message_type = 'message'
		ORDER BY message.id DESC
		LIMIT 20;
	";
	$st = $db->prepare($sql);
	$st->execute(array(':room_id' => $room_id));
	$messages = $st->fetchAll();
	
	$messages = array_reverse($messages);
	foreach($messages as &$message)
	{
		$message['timestamp'] = strtotime($message['timestamp']);
	}
	unset($message);
	
	if(!isset($_SESSION['rooms']) || !in_array($room_id, $_SESSION['rooms']))
	{
		$_SESSION['rooms'][] = $room_id;
	}
	
	$output = array(
		'room' => $room,
		'users' => $users,
		'messages' => $messages,
	);
	
	$x7->display('pages/joinroom', $output);