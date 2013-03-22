<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
	
	$x7->display('pages/roompass', array('room_id' => $room_id));