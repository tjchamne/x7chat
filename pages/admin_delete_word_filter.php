<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();

	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	if($id)
	{
		$sql = "DELETE FROM {$x7->dbprefix}word_filters WHERE id = :id";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $id,
		));
		
		$sql = "
			INSERT INTO {$x7->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':timestamp' => date('Y-m-d H:i:s'), 
			':message_type' => 'filter_resync', 
			':dest_type' => 'user', 
			':dest_id' => 0, 
			':source_type' => 'system', 
			':source_id' => 0,
		));
	}
	
	$ses->set_message($x7->lang('filter_deleted'), 'notice');
	$req->go('admin_list_word_filters');