<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$sql = "SELECT * FROM {$x7->dbprefix}rooms";
	$st = $db->prepare($sql);
	$st->execute();
	$rooms = $st->fetchAll();
	
	$x7->display('pages/roomlist', array('rooms' => $rooms));