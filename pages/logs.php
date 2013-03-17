<?php
	$x7->load('logs');
	$x7->load('messages');

	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$x7_msgs = new x7_messages();
	$x7_logs = new x7_logs();
	$logs = $x7_logs->get_room_logs(1);
	foreach($logs as &$message)
	{
		$message['timestamp_fmt'] = $x7_msgs->format_timestamp($message['timestamp']);
	}
	
	$x7->display('pages/logs', array(
		'logs' => $logs,
		'lock_user_to_self' => false,
		'allowed_sections' => array(
			'room',
			'user',
			'search',
		),
	));