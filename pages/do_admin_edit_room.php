<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$room = array();
	$room_id = isset($_POST['room_id']) ? $_POST['room_id'] : 0;
	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$topic = isset($_POST['topic']) ? $_POST['topic'] : '';
	$greeting = isset($_POST['greeting']) ? $_POST['greeting'] : '';
	$enable_password = isset($_POST['enable_password']) ? $_POST['enable_password'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	
	if($room_id)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}rooms
			WHERE
				id = :room_id
		";
		$st = $db->prepare($sql);
		$st->execute(array(':room_id' => $room_id));
		$room = $st->fetch();
		$st->closeCursor();
	}
	
	if(!$room && $room_id)
	{
		$ses->set_message($x7->lang('room_not_found'));
		$req->go('admin_list_rooms');
	}
	
	$fail = false;
	
	if(empty($name))
	{
		$ses->set_message($x7->lang('missing_room_name'));
		$fail = true;
	}
	elseif(empty($room) || $room['name'] != $name)
	{
		$sql = "
			SELECT
				1
			FROM {$x7->dbprefix}rooms
			WHERE
				name = :name
		";
		$st = $db->prepare($sql);
		$st->execute(array(':name' => $name));
		$check_room = $st->fetch();
		$st->closeCursor();
		
		if($check_room)
		{
			$ses->set_message($x7->lang('room_name_in_use'));
			$fail = true;
		}
	}
	
	if(!empty($enable_password) && empty($password) && empty($room['password']))
	{
		$ses->set_message($x7->lang('room_password_required'));
		$fail = true;
	}
	
	if(empty($fail))
	{
		if($enable_password)
		{
			if($password)
			{
				require('./includes/libraries/phpass/PasswordHash.php');
				$phpass = new \PasswordHash(8, false);
				$password = $phpass->HashPassword($password);
			}
			else
			{
				$password = $room['password'];
			}
		}
		else
		{
			$password = "";
		}
		
		$params = array(
			':name' => $name,
			':topic' => $topic,
			':greeting' => $greeting,
			':password' => $password,
		);
		
		if($room)
		{
			$sql = "
				UPDATE {$x7->dbprefix}rooms SET
					name = :name,
					topic = :topic,
					greeting = :greeting,
					password = :password
				WHERE
					id = :room_id
			";
			$params[':room_id'] = $room_id;
		}
		else
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}rooms SET
					name = :name,
					topic = :topic,
					greeting = :greeting,
					password = :password
			";
		}
		
		$st = $db->prepare($sql);
		$st->execute($params);
		
		$ses->set_message($x7->lang('room_updated'), 'notice');
		$req->go('admin_list_rooms');
	}
	else
	{
		$req->go('admin_edit_room?room_id=' . $room_id, true);
	}