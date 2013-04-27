<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	$sql = "SELECT * FROM {$x7->dbprefix}rooms";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$x7->display('pages/roomlist', array(
		'rooms' => $rooms,
		'can_view_logs' => $users->has_permission($user, 'view_logs'),
	));