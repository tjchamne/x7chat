<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$sql = "SELECT `id`, `name` FROM {$x7->dbprefix}rooms";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$sql = "SELECT * FROM {$x7->dbprefix}config LIMIT 1";
	$st = $db->prepare($sql);
	$st->execute();
	$config = $st->fetch();
	$st->closeCursor();
	
	$post = $ses->get_flash('forward');
	$config = merge($config, $post);
	
	$x7->display('pages/admin/settings', array(
		'config' => $config,
		'rooms' => $rooms,
		'menu' => $admin->generate_admin_menu('settings'),
	));