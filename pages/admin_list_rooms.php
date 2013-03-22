<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$per_page = 10;
	$page = 1;
	if(isset($_GET['page']) && (int)$_GET['page'] >= 1)
	{
		$page = (int)$_GET['page'];
	}
	
	$sql = "
		SELECT
			COUNT(*) as num
		FROM {$x7->dbprefix}rooms
	";
	$st = $db->prepare($sql);
	$st->execute();
	$count = $st->fetch();
	$st->closeCursor();
	$pages = ceil($count['num'] / $per_page);
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}rooms
	";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$pages = 5;
	
	$x7->display('pages/admin/rooms', array(
		'rooms' => $rooms,
		'paginator' => array(
			'per_page' => $per_page,
			'pages' => $pages,
			'page' => $page,
			'action' => 'admin_rooms',
		),
		'menu' => $admin->generate_admin_menu('list_rooms'),
	));