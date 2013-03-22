<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$sql = "SELECT * FROM {$x7->dbprefix}word_filters";
	$st = $db->prepare($sql);
	$st->execute();
	$filters = $st->fetchAll();
	
	$x7->display('pages/admin/word_filter', array(
		'filters' => $filters,
		'menu' => $admin->generate_admin_menu('list_word_filters'),
	));