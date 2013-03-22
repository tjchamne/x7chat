<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();

	$admin = $x7->admin();
	
	$room = array();
	$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
	
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
		$req->go('admin_rooms');
	}
	
	$post = $ses->get_flash('forward');
	$room = merge($room, $post);
	
	$x7->display('pages/admin/edit_room', array(
		'room' => $room,
		'menu' => $admin->generate_admin_menu($room_id ? 'edit_room' : 'create_room'),
	));