<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$sql = "SELECT * FROM {$x7->dbprefix}smilies";
	$st = $db->prepare($sql);
	$st->execute();
	$smilies = $st->fetchAll();
	
	$x7->display('pages/admin/smilies', array(
		'smilies' => $smilies,
		'menu' => $admin->generate_admin_menu('list_smilies'),
	));