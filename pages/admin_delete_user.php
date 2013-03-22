<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();

	$admin = $x7->admin();
	$users = $x7->users();
	
	$page_name = 'delete_user';
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	if(!$id)
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
	}
	
	$delete = $users->load_by_id($id);
	
	if($user->id == $delete->id)
	{
		$ses->set_message($x7->lang('cannot_delete_self'));
		$req->go('admin_list_users');
	}
	
	$confirmed = isset($_POST['confirm']) ? $_POST['confirm'] : 0;
	
	if($confirmed)
	{
		$users->delete($delete);
		
		$ses->set_message($x7->lang('user_deleted'), 'notice');
		$req->go('admin_list_users');
	}
	else
	{
		$x7->display('pages/admin/confirm_delete_user', array(
			'user' => $delete,
			'menu' => $admin->generate_admin_menu($page_name),
		));
	}