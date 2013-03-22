<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$smiley = array();
	$error = false;
	
	if(empty($_POST['token']))
	{
		$ses->set_message($x7->lang('token_required'));
		$error = true;
	}
	else
	{
		$smiley[':token'] = $_POST['token'];
	}
	
	if(empty($_POST['image']))
	{
		$ses->set_message($x7->lang('image_required'));
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
		$req->go('admin_edit_smiley', true);
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
		
		$ses->set_message($x7->lang('admin_smilies_updated'), 'notice');
		$req->go('admin_list_smilies');
	}