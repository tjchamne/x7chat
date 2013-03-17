<?php
	$db = $x7->db();
	
	if(empty($_SESSION['user_id']))
	{
		$x7->fatal_error($x7->lang('login_required'));
	}
	
	$user_id = $_SESSION['user_id'];
	
	$sql = "
		SELECT
			*
		FROM {$x7->dbprefix}users
		WHERE
			id = :user_id
	";
	$st = $db->prepare($sql);
	$st->execute(array(
		'user_id' => $user_id,
	));
	$user = $st->fetch();
	$st->closeCursor();
	
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$real_name = isset($_POST['real_name']) ? $_POST['real_name'] : '';
	$bio = isset($_POST['bio']) ? $_POST['bio'] : '';
	$enable_sounds = isset($_POST['enable_sounds']) ? $_POST['enable_sounds'] : '';
	$enable_styles = isset($_POST['enable_styles']) ? $_POST['enable_styles'] : '';
	$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
	$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
	$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
	$retype_new_password = isset($_POST['retype_new_password']) ? $_POST['retype_new_password'] : '';
	
	$message_font_size = isset($_POST['message_font_size']) ? (int)$_POST['message_font_size'] : '';
	$message_font_color = isset($_POST['message_font_color']) ? $_POST['message_font_color'] : '';
	$message_font_face = isset($_POST['message_font_face']) ? (int)$_POST['message_font_face'] : '';
	
	$use_default_timestamp_settings = isset($_POST['use_default_timestamp_settings']) ? (int)$_POST['use_default_timestamp_settings'] : '';
	$enable_timestamps = isset($_POST['enable_timestamps']) ? $_POST['enable_timestamps'] : '';
	$ts_24_hour = isset($_POST['ts_24_hour']) ? (int)$_POST['ts_24_hour'] : '';
	$ts_show_seconds = isset($_POST['ts_show_seconds']) ? (int)$_POST['ts_show_seconds'] : '';
	$ts_show_ampm = isset($_POST['ts_show_ampm']) ? $_POST['ts_show_ampm'] : '';
	$ts_show_date = isset($_POST['ts_show_date']) ? (int)$_POST['ts_show_date'] : '';
	
	$location = isset($_POST['location']) ? $_POST['location'] : '';
	$status_description = isset($_POST['status_description']) ? $_POST['status_description'] : '';
	
	$status_type = isset($_POST['status_type']) ? $_POST['status_type'] : 'available';
	if(!in_array($status_type, array('available', 'busy', 'away')))
	{
		$status_type = 'available';
	}
	
	if($status_type != $user['status_type'])
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
					':message' => 'leave_rooms',
					':dest_type' => 'room', 
					':dest_id' => $room, 
					':source_type' => 'system', 
					':source_id' => 0,
				));
			}
		}
	}
	
	$data = array(
		':user_id' => $user_id,
		':real_name' => $real_name, 
		':bio' => $bio, 
		':gender' => $gender,
		':enable_sounds' => (bool)$enable_sounds,
		':enable_styles' => (bool)$enable_styles,
		':message_font_size' => $message_font_size,
		':message_font_color' => $message_font_color,
		':message_font_face' => $message_font_face,
		':use_default_timestamp_settings' => $use_default_timestamp_settings,
		':enable_timestamps' => $enable_timestamps,
		':ts_24_hour' => $ts_24_hour,
		':ts_show_seconds' => $ts_show_seconds,
		':ts_show_ampm' => $ts_show_ampm,
		':ts_show_date' => $ts_show_date,
		':location' => $location,
		':status_type' => $status_type,
		':status_description' => $status_description
	);
	
	$fields = '';
	
	$priv_change = false;
	$fail = false;
	
	if($message_font_size)
	{
		$min_font_size = $x7->config('min_font_size');
		$max_font_size = $x7->config('max_font_size');
		
		if($message_font_size < $min_font_size)
		{
			$fail = true;
			$x7->set_message($x7->lang('min_font_size_error', array(
				':size' => $min_font_size,
			)));
		}
		
		if($message_font_size > $max_font_size)
		{
			$fail = true;
			$x7->set_message($x7->lang('max_font_size_error', array(
				':size' => $max_font_size,
			)));
		}
	}
	
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
			$x7->set_message($x7->lang('font_face_error'));
		}
	}
	
	if($message_font_color)
	{
		if(!preg_match('#^[A-F0-9]{6}$#i', $message_font_color))
		{
			$fail = true;
			$x7->set_message($x7->lang('color_error'));
		}
	}
	
	if($user['email'] != $user['username'])
	{
		require('./includes/libraries/phpass/PasswordHash.php');
		$phpass = new PasswordHash(8, false);
		
		if($user['email'] != $email)
		{
			$priv_change = true;
			$data[':email'] = $email;
			$fields .= ',email = :email';
		}
		
		if($new_password || $retype_new_password)
		{
			if($new_password != $retype_new_password)
			{
				$fail = true;
				$x7->set_message($x7->lang('passwords_donot_match'));
			}
			
			$priv_change = true;
			$hashed_password = $phpass->HashPassword($new_password);
			$data[':password'] = $hashed_password;
			$fields .= ',password = :password';
		}
		
		if($priv_change)
		{
			if(!$current_password || !$user['password'] || !$phpass->CheckPassword($current_password, $user['password']))
			{
				$fail = true;
				$x7->set_message($x7->lang('current_password_wrong'));
			}
		}
	}
	
	if(!empty($_POST['remove_avatar']))
	{
		$old_avatar = $user['avatar'];
		if($old_avatar)
		{
			@unlink('uploads/' . $old_avatar);
			@unlink('uploads/' . 'mini_' . $old_avatar);
			@unlink('uploads/' . 'normal_' . $old_avatar);
		}
		
		$fields .= ',avatar = :avatar';
		$data[':avatar'] = '';
	}
	elseif(!empty($_FILES['avatar']['name']))
	{
		$extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
		if(!in_array($extension, array('png', 'jpg', 'jpeg', 'gif')))
		{
			$fail = true;
			$x7->set_message($x7->lang('invalid_avatar_extension'));
		}
		else
		{
			$old_avatar = $user['avatar'];
			if($old_avatar)
			{
				@unlink('uploads/' . $old_avatar);
				@unlink('uploads/' . 'mini_' . $old_avatar);
				@unlink('uploads/' . 'normal_' . $old_avatar);
			}
			
			$count = 0;
			do {
				$new_avatar = 'avatar_' . sha1(uniqid('', true)) . '.' . $extension;
				$count++;
			} while($count < 5 && file_exists('uploads/' . $new_avatar));
			
			move_uploaded_file($_FILES["avatar"]["tmp_name"], 'uploads/' . $new_avatar);
			
			require_once('includes/libraries/phpthumb/ThumbLib.inc.php');
			$thumb = PhpThumbFactory::create('uploads/' . $new_avatar);
			$thumb->resize(16, 16);
			$thumb->save('uploads/' . 'mini_' . $new_avatar);
			
			$thumb = PhpThumbFactory::create('uploads/' . $new_avatar);
			$thumb->resize(100, 100);
			$thumb->save('uploads/' . 'normal_' . $new_avatar);
			
			$fields .= ',avatar = :avatar';
			$data[':avatar'] = $new_avatar;
		}
	}
	
	if(!$fail)
	{
		$sql = "
			UPDATE {$x7->dbprefix}users SET
				real_name = :real_name,
				about = :bio,
				gender = :gender,
				enable_sounds = :enable_sounds,
				enable_styles = :enable_styles,
				message_font_size = :message_font_size,
				message_font_color = :message_font_color,
				message_font_face = :message_font_face,
				use_default_timestamp_settings = :use_default_timestamp_settings,
				enable_timestamps = :enable_timestamps,
				ts_24_hour = :ts_24_hour,
				ts_show_seconds = :ts_show_seconds,
				ts_show_ampm = :ts_show_ampm,
				ts_show_date = :ts_show_date,
				status_type = :status_type,
				status_description = :status_description,
				location = :location
				{$fields}
			WHERE
				id = :user_id
		";
		$st = $db->prepare($sql);
		$st->execute($data);
		
		$x7->set_message($x7->lang('settings_updated'), 'notice');
		//$x7->go('settings');
	}
	else
	{
		//$x7->go('settings', $_POST);
		$_SESSION['vars'] = $_POST;
	}
	
	$x7->display('pages/savesettings');