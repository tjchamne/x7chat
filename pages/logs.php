<?php
	$x7->load('logs');

	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$x7_logs = new x7_logs();
	//$logs = $x7_logs->get_room_logs(1);
	$logs = $x7_logs->get_user_logs(1, 3);
	
	$x7->display('pages/logs', array(
		'logs' => $logs,
	));