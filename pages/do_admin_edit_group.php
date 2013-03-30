<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$groups = array();
	$id = isset($_POST['id']) ? $_POST['id'] : 0;
	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$access_admin_panel = isset($_POST['access_admin_panel']) ? 1 : 0;
	$create_room = isset($_POST['create_room']) ? 1 : 0;
	$view_logs = isset($_POST['view_logs']) ? 1 : 0;
	$view_unrestricted_logs = isset($_POST['view_unrestricted_logs']) ? 1 : 0;
	$view_private_logs = isset($_POST['view_private_logs']) ? 1 : 0;
	
	$group = null;
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
	
	$fail = false;
	
	if(empty($name))
	{
		$ses->set_message($x7->lang('missing_group_name'));
		$fail = true;
	}
	elseif(empty($group) || $group['name'] != $name)
	{
		$sql = "
			SELECT
				1
			FROM {$x7->dbprefix}groups
			WHERE
				name = :name
		";
		$st = $db->prepare($sql);
		$st->execute(array(':name' => $name));
		$check_group = $st->fetch();
		$st->closeCursor();
		
		if($check_group)
		{
			$ses->set_message($x7->lang('group_name_in_use'));
			$fail = true;
		}
	}
	
	if(empty($fail))
	{
		$params = array(
			':name' => $name,
			':access_admin_panel' => $access_admin_panel,
			':create_room' => $create_room,
			':view_logs' => $view_logs,
			':view_unrestricted_logs' => $view_unrestricted_logs,
			':view_private_logs' => $view_private_logs,
		);
		
		if($group)
		{
			$sql = "
				UPDATE {$x7->dbprefix}groups SET
					name = :name,
					access_admin_panel = :access_admin_panel,
					create_room = :create_room,
					view_logs = :view_logs,
					view_unrestricted_logs = :view_unrestricted_logs,
					view_private_logs = :view_private_logs
				WHERE
					id = :id
			";
			$params[':id'] = $id;
		}
		else
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}groups SET
					name = :name,
					access_admin_panel = :access_admin_panel,
					create_room = :create_room,
					view_logs = :view_logs,
					view_unrestricted_logs = :view_unrestricted_logs,
					view_private_logs = :view_private_logs
			";
		}
		
		$st = $db->prepare($sql);
		$st->execute($params);
		
		$ses->set_message($x7->lang('group_updated'), 'notice');
		$req->go('admin_list_groups');
	}
	else
	{
		$req->go('admin_edit_group?id=' . $id, true);
	}