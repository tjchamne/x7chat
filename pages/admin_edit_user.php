<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();
	
	$users = $x7->users();
	$admin = $x7->admin();

	$user_id = isset($_GET['id']) ? $_GET['id'] : 0;
	
	if($user_id)
	{
		$edit_user = $users->load_by_id($user_id);
	}
	else
	{
		$edit_user = new model\user(array(
			'real_name' => '',
			'about' => '',
			'enable_sounds' => '',
			'enable_styles' => '',
			'gender' => '',
			'message_font_size' => '',
			'message_font_color' => '',
			'message_font_face' => '',
			'use_default_timestamp_settings' => '',
			'enable_timestamps' => '',
			'ts_24_hour' => '',
			'ts_show_seconds' => '',
			'ts_show_ampm' => '',
			'ts_show_date' => '',
			'location' => '',
			'status_description' => '',
			'email' => '',
			'username' => '',
			'avatar' => '',
			'id' => '',
		));
	}
	
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
	$defaults = merge(clone $edit_user, $post);
	
	$x7->display('pages/admin/edit_user', array(
		'menu' => $admin->generate_admin_menu($user_id ? 'edit_user' : 'create_user'),
		'genders' => $genders, 
		'user' => $users->output($defaults),
		'fonts' => $fonts,
		'action' => 'savesettings?from=admin',
		'allow_username_edit' => true,
		'require_password_confirm' => false,
		'disable_accounts' => false,
	));