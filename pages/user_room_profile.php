<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	// Get the user being shown
	$view_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $user->id;
	$view_user = $users->load_by_id($view_user_id);
	
	$show_ip = $users->has_permission($user, 'access_admin_panel');
	$allow_ban = $users->has_permission($user, 'access_admin_panel');
	
	$x7->display('pages/user_room_profile', array(
		'user' => $view_user,
		'show_ip' => $show_ip,
		'allow_ban' => $allow_ban,
		'show_avatar' => $x7->supports_image_uploads(),
	));