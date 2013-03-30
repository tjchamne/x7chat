<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();

	$admin = $x7->admin();
	
	$group = array();
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	if($id)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}groups
			WHERE
				id = :id
		";
		$st = $db->prepare($sql);
		$st->execute(array(':id' => $id));
		$group = $st->fetch();
		$st->closeCursor();
	}
	
	if(!$group && $id)
	{
		$ses->set_message($x7->lang('group_not_found'));
		$req->go('admin_list_groups');
	}
	
	$post = $ses->get_flash('forward');
	$group = merge($group, $post);
	
	$x7->display('pages/admin/edit_group', array(
		'group' => $group,
		'menu' => $admin->generate_admin_menu($id ? 'edit_group' : 'create_group'),
	));