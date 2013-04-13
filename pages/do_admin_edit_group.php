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
	$color = isset($_POST['color']) ? $_POST['color'] : '';
	
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
	
	if(!empty($_FILES['avatar']['name']))
	{
		$extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
		if(!in_array($extension, array('png', 'jpg', 'jpeg', 'gif')))
		{
			$fail = true;
			$ses->set_message($x7->lang('invalid_avatar_extension'));
		}
	}
	
	if(empty($fail))
	{
		$extra_fields = '';
		$params = array(
			':name' => $name,
			':access_admin_panel' => $access_admin_panel,
			':create_room' => $create_room,
			':view_logs' => $view_logs,
			':view_unrestricted_logs' => $view_unrestricted_logs,
			':view_private_logs' => $view_private_logs,
			':color' => $color,
		);
		
		if($group && (!empty($_POST['remove_avatar']) || !empty($_FILES['avatar']['name'])))
		{
			$old_avatar = $group['image'];
			if($old_avatar)
			{
				@unlink('uploads/' . $old_avatar);
				@unlink('uploads/' . 'mini_' . $old_avatar);
				@unlink('uploads/' . 'normal_' . $old_avatar);
			}
			
			$extra_fields = ",image = :image";
			$params[':image'] = '';
		}
		
		if(!empty($_FILES['avatar']['name']))
		{
			$extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
			
			$count = 0;
			do {
				$new_avatar = 'avatar_' . sha1(uniqid('', true)) . '.' . $extension;
				$count++;
			} while($count < 5 && file_exists('uploads/' . $new_avatar));
			
			move_uploaded_file($_FILES["avatar"]["tmp_name"], 'uploads/' . $new_avatar);
			
			require_once('includes/libraries/phpthumb/ThumbLib.inc.php');
			$thumb = \PhpThumbFactory::create('uploads/' . $new_avatar);
			$thumb->resize(16, 16);
			$thumb->save('uploads/' . 'mini_' . $new_avatar);
			
			$thumb = \PhpThumbFactory::create('uploads/' . $new_avatar);
			$thumb->resize(100, 100);
			$thumb->save('uploads/' . 'normal_' . $new_avatar);
			
			$extra_fields = ",image = :image";
			$params[':image'] = $new_avatar;
		}
		
		if($group)
		{
			$sql = "
				UPDATE {$x7->dbprefix}groups SET
					name = :name,
					access_admin_panel = :access_admin_panel,
					create_room = :create_room,
					view_logs = :view_logs,
					view_unrestricted_logs = :view_unrestricted_logs,
					color = :color,
					view_private_logs = :view_private_logs
					{$extra_fields}
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
					color = :color,
					view_private_logs = :view_private_logs
					{$extra_fields}
			";
		}
		
		$st = $db->prepare($sql);
		$st->execute($params);
		
		$ses->set_message($x7->lang('group_updated'), 'notice');
		$goto = 'admin_list_groups';
	}
	else
	{
		$ses->set_flash('forward', $_POST);
		$goto = 'admin_edit_group?id=' . $id;
	}
	
	$x7->display('pages/admin/savegroup', array(
		'errors' => array(),
		'notices' => array(),
		'goto' => $goto,
	));