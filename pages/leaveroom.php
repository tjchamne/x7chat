<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	$room_id = isset($_POST['room']) ? $_POST['room'] : array();
	
	foreach($_SESSION['rooms'] as $key => $id)
	{
		if($id == $room_id)
		{
			unset($_SESSION['rooms'][$key]);
			$users->leave_rooms($user, array($room_id));
			break;
		}
	}