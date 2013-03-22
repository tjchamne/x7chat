<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$logs = $x7->logs();
	
	$messages = $logs->get_room_logs($user, 1);
	foreach($messages as &$message)
	{
		$message['timestamp_fmt'] = $message['timestamp'];//$x7_msgs->format_timestamp($message['timestamp']);
	}
	
	$rooms = $logs->get_visible_rooms($user);
	
	$x7->display('pages/logs', array(
		'logs' => $messages,
		'lock_user_to_self' => false,
		'allowed_sections' => array(
			'room',
			'user',
			'search',
		),
		'rooms' => $rooms,
	));