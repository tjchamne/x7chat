<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}word_filters
		ORDER BY
			LENGTH(word) DESC
	";
	$st = $db->prepare($sql);
	$st->execute();
	$filters = $st->fetchAll();
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}smilies
		ORDER BY
			LENGTH(token) DESC
	";
	$st = $db->prepare($sql);
	$st->execute();
	$smilies = $st->fetchAll();
	
	$access_acp = $users->has_permission($user, 'access_admin_panel');
	
	$auto_join = 0;
	if(empty($_SESSION['rooms']))
	{
		$auto_join = $x7->config('auto_join');
	}
	
	$x7->display('pages/chat', array(
		'user' => $users->output($user),
		'access_acp' => $access_acp, 
		'auto_join' => $auto_join,
		'filters' => $filters,
		'smilies' => $smilies,
	));