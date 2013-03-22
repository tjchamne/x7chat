<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$logs = $x7->logs();
	
	$query = $req->post('query');

	$output = array(
		'results' => array(),
	);
	
	if($query)
	{
		$sql = "
			SELECT
				id,
				username
			FROM {$x7->dbprefix}users
			WHERE
				username LIKE :query
			LIMIT 10;
		";
		$st = $x7->db()->prepare($sql);
		$st->execute(array(':query' => '%' . $query . '%'));
		$rows = $st->fetchAll();
		
		foreach($rows as $row)
		{
			$output['results'][] = array(
				'id' => $row['id'], 
				'text' => $row['username']
			);
		}
	}
	
	echo json_encode($output);
	exit;
	
	/*
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
	*/