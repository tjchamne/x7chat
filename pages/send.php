<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$room_id = isset($_POST['room']) ? $_POST['room'] : array();
	$dest_type = isset($_POST['dest_type']) ? $_POST['dest_type'] : array();
	$message = isset($_POST['message']) ? $_POST['message'] : array();

	$server_rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
	
	$font_color = $user->message_font_color;
	$font_size = $user->message_font_size;
	$font_face = $user->message_font_face;
	
	if($font_face)
	{
		if(!isset($_SESSION['cache']['font_face'][$font_face]))
		{
			$sql = "
				SELECT
					font
				FROM {$x7->dbprefix}message_fonts
				WHERE
					id = :id
				LIMIT 1;
			";
			$st = $db->prepare($sql);
			$st->execute(array(
				':id' => $font_face,
			));
			$face = $st->fetch();
			$st->closeCursor();
			
			if($face)
			{
				$_SESSION['cache']['font_face'][$font_face] = $face['font'];
			}
			else
			{
				$_SESSION['cache']['font_face'][$font_face] = '';
			}
		}
		
		$font_face = $_SESSION['cache']['font_face'][$font_face];
	}
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (
			timestamp, 
			message_type, 
			sender_name,
			dest_type, 
			dest_id, 
			source_type, 
			source_id, 
			message,
			font_size,
			font_color,
			font_face
		) VALUES (
			:timestamp, 
			:message_type, 
			:sender_name,
			:dest_type, 
			:dest_id, 
			:source_type, 
			:source_id, 
			:message,
			:font_size,
			:font_color,
			:font_face
		)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'message', 
		':sender_name' => $user->username,
		':message' => $message,
		':dest_type' => $dest_type, 
		':dest_id' => $room_id, 
		':source_type' => 'user', 
		':source_id' => $user->id,
		':font_size' => $font_size,
		':font_color' => $font_color,
		':font_face' => $font_face
	));