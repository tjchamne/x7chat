<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		die(json_encode(array('redirect' => $x7->url('login'))));
	}
	
	$room_id = isset($_POST['room']) ? $_POST['room'] : array();
	$dest_type = isset($_POST['dest_type']) ? $_POST['dest_type'] : array();
	$message = isset($_POST['message']) ? $_POST['message'] : array();

	$server_rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
	
	$user_id = $_SESSION['user_id'];
	
	$sql = "
		INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id, message) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id, :message)
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		':timestamp' => date('Y-m-d H:i:s'), 
		':message_type' => 'message', 
		':message' => $message,
		':dest_type' => $dest_type, 
		':dest_id' => $room_id, 
		':source_type' => 'user', 
		':source_id' => $user_id,
	));