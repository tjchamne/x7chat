<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$admin = $x7->admin();
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if(!$id)
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
	}
	
	$filter = array();
	
	if($id)
	{
		$sql = "SELECT * FROM {$x7->dbprefix}word_filters WHERE id = :id";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $id,
		));
		$filter = $st->fetch();
		$st->closeCursor();
	}
	
	$x7->display('pages/admin/edit_word_filter', array(
		'menu' => $admin->generate_admin_menu($id ? 'edit_word_filter' : 'create_word_filter'),
		'filter' => $filter,
	));