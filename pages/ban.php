<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('ban_users');
	$ses->check_bans();
	
	$users = $x7->users();
	
	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
	$by = isset($_GET['by']) ? $_GET['by'] : 0;
	
	if(!$user_id)
	{
		throw new exception("Missing parameter value for user_id");
	}
	
	if(!in_array($by, array('ip', 'account')))
	{
		throw new exception("Invalid parameter value for by");
	}
	
	$banning_user = $users->load_by_id($user_id);;
	
	if(!$banning_user->ip)
	{
		$x7->fatal_error($x7->lang('login_failed_banned_unknown_ip'));
	}
	
	$x7->display('pages/banconfirm', array(
		'user' => $banning_user,
		'by' => $by,
	));