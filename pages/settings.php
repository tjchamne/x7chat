<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$users = $x7->users();
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}message_fonts
	";
	$st = $db->prepare($sql);
	$st->execute();
	$fonts = $st->fetchAll();
	
	$genders = array(
		'male' => $x7->lang('male'),
		'female' => $x7->lang('female'),
	);
	
	$post = $ses->get_flash('forward');
	$defaults = merge(clone $user, $post);
	
	$x7->display('pages/settings', array(
		'genders' => $genders, 
		'user' => $users->output($defaults),
		'fonts' => $fonts,
		'action' => 'savesettings',
		'allow_username_edit' => false,
		'require_password_confirm' => true,
		'disable_accounts' => (!$user->password || $user->password === 'x'),
	));