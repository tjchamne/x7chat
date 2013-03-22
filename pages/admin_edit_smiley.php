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
	
	$smiley = array();
	
	if($id)
	{
		$sql = "SELECT * FROM {$x7->dbprefix}smilies WHERE id = :id";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $id,
		));
		$smiley = $st->fetch();
		$st->closeCursor();
	}
	
	$images = array();
	
	$sql = "SELECT * FROM {$x7->dbprefix}smilies";
	$st = $db->prepare($sql);
	$st->execute();
	$smilies = $st->fetchAll();
	foreach($smilies as $data)
	{
		$images[realpath($data['image'])] = true;
	}
	
	$uploaded = scandir('./smilies/');
	foreach($uploaded as $file)
	{
		if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), array('png', 'gif', 'jpg', 'jpeg')))
		{
			if(!isset($images[realpath('./smilies/' . $file)]))
			{
				$images['smilies/' . $file] = false;
			}
		}
	}
	
	$x7->display('pages/admin/edit_smiley', array(
		'menu' => $admin->generate_admin_menu($id ? 'edit_smiley' : 'create_smiley'),
		'smiley' => $smiley,
		'smilies' => $images,
	));