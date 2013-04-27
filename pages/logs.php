<?php

	namespace x7;

	$user = $ses->current_user();
	$req->require_permission('view_logs');
	$ses->check_bans();
	
	$logs = $x7->logs();
	$msglib = $x7->messages();
	
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
	$room = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : '';
	
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '';
	$end = isset($_REQUEST['end']) ? $_REQUEST['end'] : '';
	
	$mode = isset($_REQUEST['log_view_mode']) ? $_REQUEST['log_view_mode'] : 'show';
	
	// Only room logs are supported right now
	if(!$room || $type !== 'room')
	{
		$req->go('roomlist');
	}
	
	$messages = $logs->get_room_logs($user, $room, $start, $end);
	foreach($messages as &$message)
	{
		$message['timestamp_fmt'] = $msglib->format_timestamp($user, $message['timestamp']);
	}
	
	$vdata = array(
		'logs' => $messages,
		'id' => $room,
	);
	
	if($mode === 'download')
	{
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="log.txt"');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		$x7->display('download_logs', $vdata);
		exit;
	}
	else
	{	
		$x7->display('pages/logs', $vdata);
	}