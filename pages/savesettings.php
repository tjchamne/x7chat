<?php

	namespace x7;
	
	$user = $ses->current_user();
	$ses->check_bans();
	
	$auth = $x7->auth();
	$users = $x7->users();
	
	$from = isset($_GET['from']) ? $_GET['from'] : '';
	$is_admin = ($from === 'admin');
	
	if($is_admin)
	{
		$req->require_permission('access_admin_panel');
		if(!empty($_POST['id']))
		{
			$edit_user = $users->load_by_id($_POST['id']);
		}
		else
		{
			$edit_user = new model\user(array(
				'real_name' => '',
				'group_id' => '',
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
				'status_type' => '',
				'email' => '',
				'username' => '',
				'avatar' => '',
				'id' => '',
			));
		}
	}
	else
	{
		// User to edit
		$edit_user = $users->load_by_id($user->id);
	}
	
	$fail = false;
	
	// "Protected" fields to edit
	// Changing these requires entering a current password
	$account_fields = array();
	
	if($is_admin)
	{
		$account_fields[] = 'username';
	}
	
	// Unprotected fields to edit
	$profile_fields = array(
		'real_name',
		'about',
		'enable_sounds',
		'enable_styles',
		'gender',
		'message_font_size',
		'message_font_color',
		'message_font_face',
		'use_default_timestamp_settings',
		'enable_timestamps',
		'ts_24_hour',
		'ts_show_seconds',
		'ts_show_ampm',
		'ts_show_date',
		'location',
		'status_description',
	);
	
	$status_type = $req->post('status_type');
	if(!in_array($status_type, array('available', 'busy', 'away')))
	{
		$status_type = 'available';
	}
	$profile_fields['status_type'] = $status_type;
	$reset_status = ($edit_user->status_type != $status_type);
	
	$message_font_size = $req->post('message_font_size');
	if($message_font_size)
	{
		$min_font_size = $x7->config('min_font_size');
		$max_font_size = $x7->config('max_font_size');
		
		if($message_font_size < $min_font_size)
		{
			$fail = true;
			$ses->set_message($x7->lang('min_font_size_error', array(
				':size' => $min_font_size,
			)));
		}
		
		if($message_font_size > $max_font_size)
		{
			$fail = true;
			$ses->set_message($x7->lang('max_font_size_error', array(
				':size' => $max_font_size,
			)));
		}
	}
	
	$message_font_face= $req->post('message_font_face');
	if($message_font_face)
	{
		$sql = "
			SELECT
				*
			FROM {$x7->dbprefix}message_fonts
			WHERE
				id = :id
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':id' => $message_font_face,
		));
		$font = $st->fetchAll();
		$st->closeCursor();
		
		if(!$font)
		{
			$fail = true;
			$ses->set_message($x7->lang('font_face_error'));
		}
	}
	
	$message_font_color= $req->post('message_font_color');
	if($message_font_color)
	{
		if(!preg_match('#^[A-F0-9]{6}$#i', $message_font_color))
		{
			$fail = true;
			$ses->set_message($x7->lang('color_error'));
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
	
	// Account detail updates are only allowed if the user isn't a guest and the chatroom isn't integrated
	if($is_admin || ($edit_user->password && $edit_user->password !== 'x'))
	{
		if($is_admin)
		{
			$username = $req->post('username');
			if(empty($username))
			{
				$fail = true;
				$ses->set_message($x7->lang('missing_login_username'));
			}
		}
	
		$email = $req->post('email');
		if($email != $edit_user->email)
		{
			$account_fields[] = 'email';
		}
		
		$password = $req->post('password');
		$retype_new_password = $req->post('retype_new_password');
		if($password)
		{
			if($password !== $retype_new_password)
			{
				$fail = true;
				$ses->set_message($x7->lang('passwords_donot_match'));
			}
			else
			{
				$account_fields['password'] = $auth->hash_password($password);
			}
		}
	
		if(!empty($account_fields) && !$is_admin)
		{
			$current_password = $req->post('current_password');
			try
			{
				$auth->authenticate($edit_user->username, $current_password);
			}
			catch(exception\authentication_exception $ex)
			{
				$fail = true;
				$ses->set_message($x7->lang('current_password_wrong'));
			}
		}
	}
	
	if($is_admin)
	{
		$group = $req->post('group_id');
		if($group)
		{
			$account_fields[] = 'group_id';
		}
	}
	
	if(!$fail)
	{
		if(!empty($_POST['remove_avatar']) || !empty($_FILES['avatar']['name']))
		{
			$old_avatar = $edit_user->avatar;
			if($old_avatar)
			{
				@unlink('uploads/' . $old_avatar);
				@unlink('uploads/' . 'mini_' . $old_avatar);
				@unlink('uploads/' . 'normal_' . $old_avatar);
			}
			
			$profile_fields['avatar'] = '';
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
			
			$profile_fields['avatar'] = $new_avatar;
		}
		
		$fields = array_merge($profile_fields, $account_fields);
		foreach($fields as $key => $field)
		{
			if(is_numeric($key))
			{
				$edit_user->$field = (string)$req->post($field);
			}
			else
			{
				$edit_user->$key = $field;
				unset($fields[$key]);
				$fields[] = $key;
			}
		}
		
		$users->save_user($edit_user, $fields);
		
		if($edit_user->id === $user->id)
		{
			$ses->current_user($edit_user);
		}
	
		if($reset_status)
		{
			$rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
			if($rooms)
			{
				foreach($rooms as $room)
				{
					$sql = "
						INSERT INTO {$x7->dbprefix}messages (timestamp, message, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message, :message_type, :dest_type, :dest_id, :source_type, :source_id)
					";
					$st = $x7->db()->prepare($sql);
					$st->execute(array(
						':timestamp' => date('Y-m-d H:i:s'), 
						':message_type' => 'room_resync', 
						':message' => 'room_resync',
						':dest_type' => 'room', 
						':dest_id' => $room, 
						':source_type' => 'system', 
						':source_id' => 0,
					));
				}
			}
		}
		
		$ses->set_message($x7->lang('settings_updated'), 'notice');
	}
	else
	{
		$ses->set_flash('forward', $_POST);
	}
	
	$x7->display('pages/savesettings', array(
		'errors' => array(),
		'notices' => array(),
		'is_admin' => $is_admin,
		'user' => $edit_user,
	));