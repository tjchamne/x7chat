<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$page_name = 'list_groups';
	
	$per_page = 15;
	$page = 1;
	if(isset($_GET['pg']) && (int)$_GET['pg'] >= 1)
	{
		$page = (int)$_GET['pg'];
	}
	
	$sql = "
		SELECT
			COUNT(*) as num
		FROM {$x7->dbprefix}groups
	";
	$st = $db->prepare($sql);
	$st->execute();
	$count = $st->fetch();
	$st->closeCursor();
	$pages = ceil($count['num'] / $per_page);
	
	$start = $per_page*($page-1);
	$end = $start+$per_page;
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}groups
		ORDER BY
			name ASC
		LIMIT {$start}, {$end}
	";
	$st = $db->prepare($sql);
	$st->execute();
	$groups = $st->fetchAll();
	
	$x7->display('pages/admin/groups', array(
		'groups' => $groups,
		'paginator' => array(
			'per_page' => $per_page,
			'pages' => $pages,
			'page' => $page,
			'action' => 'admin_list_groups?pg=',
		),
		'menu' => $admin->generate_admin_menu($page_name),
	));