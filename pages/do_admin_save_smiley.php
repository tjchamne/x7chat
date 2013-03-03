<?php
	$x7->load('user');
	
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user = new x7_user();
	$perms = $user->permissions();
	if(empty($perms['access_admin_panel']))
	{
		$x7->fatal_error($x7->lang('access_denied'));
	}
	
	$smiley = array();
	$error = false;
	
	if(empty($_POST['token']))
	{
		$x7->set_message($x7->lang('token_required'));
		$error = true;
	}
	else
	{
		$smiley[':token'] = $_POST['token'];
	}
	
	if(empty($_POST['image']))
	{
		$x7->set_message($x7->lang('image_required'));
		$error = true;
	}
	else
	{
		$smiley[':image'] = $_POST['image'];
	}
	
	if(!empty($_POST['id']))
	{
		$smiley[':id'] = $_POST['id'];
	}
	
	if($error)
	{
		$x7->go('admin_edit_smiley', array('smiley' => $_POST));
	}
	else
	{
		if(!empty($smiley[':id']))
		{
			$sql = "
				UPDATE {$x7->dbprefix}smilies SET
					token = :token,
					image = :image
				WHERE
					id = :id
				LIMIT 1
			";
		}
		else
		{
			$sql = "
				INSERT INTO {$x7->dbprefix}smilies (token, image) VALUES (:token, :image)";
		}
	
		$st = $db->prepare($sql);
		$st->execute($smiley);

		$sql = "
			INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':timestamp' => date('Y-m-d H:i:s'), 
			':message_type' => 'smiley_resync', 
			':dest_type' => 'user', 
			':dest_id' => 0, 
			':source_type' => 'system', 
			':source_id' => 0,
		));
		
		$x7->set_message($x7->lang('admin_smilies_updated'), 'notice');
		$x7->go('admin_list_smilies');
	}