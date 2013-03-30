<form class="standard_form" id="settings_form" enctype="multipart/form-data" target="submit_iframe" method="post" action="<?php echo $url($action); ?>">
	<input type="hidden" name="id" value="<?php echo $user->id; ?>" />

	<?php if(!$disable_accounts): ?>
		<h2><?php $lang('account_settings'); ?></h2>
		
		<?php if($allow_username_edit): ?>
			<label for="username"><?php $lang('username'); ?></label>
			<input type="text" name="username" id="username" value="<?php $esc($user->username); ?>" />
			<p>&nbsp;</p>
		<?php endif; ?>
		
		<?php if($require_password_confirm): ?>
			<label for="current_password"><?php $lang('current_password'); ?></label>
			<input type="password" name="current_password" id="current_password" />
			<p><?php $lang('current_password_instr'); ?></p>
		<?php endif; ?>
		
		<label for="password"><?php $lang('new_password'); ?></label>
		<input type="password" name="password" id="password" />
		<p><?php $lang('new_password_instr'); ?></p>
		
		<label for="retype_new_password"><?php $lang('retype_new_password'); ?></label>
		<input type="password" name="retype_new_password" id="retype_new_password" />
		<p><?php $lang('retype_new_password_instr'); ?></p>
		
		<label for="email"><?php $lang('email_label'); ?></label>
		<input type="text" name="email" id="email" value="<?php $esc($user->email); ?>" />
		<p>&nbsp;</p>
		
		<?php if(!empty($allow_group_change)): ?>
			<label for="group_id"><?php $lang("user_group"); ?></label>
			<select name="group_id" id="group_id">
				<?php foreach($groups as $group): ?>
					<option value="<?php $esc($group['id']); ?>" <?php if($user->group_id == $group['id']) echo "selected"; ?>><?php $esc($group['name']); ?></option>
				<?php endforeach; ?>
			</select>
		<?php endif; ?>
	<?php endif; ?>
	
	<h2><?php $lang('profile_settings'); ?></h2>
	<label for="real_name"><?php $lang('real_name_label'); ?></label>
	<input type="text" name="real_name" id="real_name" value="<?php $esc($user->real_name); ?>" />
	<p>&nbsp;</p>
	
	<label for="location"><?php $lang('location_label'); ?></label>
	<input type="text" name="location" id="location" value="<?php $esc($user->location); ?>" />
	<p>&nbsp;</p>
	
	<label for="status_description"><?php $lang('status_description_label'); ?></label>
	<input type="text" name="status_description" id="status_description" value="<?php $esc($user->status_description); ?>" />
	<p>&nbsp;</p>
	
	<label for="status_type"><?php $lang('status_type_label'); ?></label>
	<select name="status_type" id="status_type">
		<option value="available" <?php if($user->status_type == 'available') echo 'selected'; ?>><?php $lang('available_option'); ?></option>
		<option value="busy" <?php if($user->status_type == 'busy') echo 'selected'; ?>><?php $lang('busy_option'); ?></option>
		<option value="away" <?php if($user->status_type == 'away') echo 'selected'; ?>><?php $lang('away_option'); ?></option>
	</select>
	<p>&nbsp;</p>
	
	<?php if($allow_avatar): ?>
		<label for="avatar"><?php $lang('avatar_label'); ?></label>
		<div style="display: inline-block;">
			<?php if($user->avatar): ?>
				<img src="uploads/normal_<?php echo $user->avatar; ?>" />
				<br />
				<input type="checkbox" name="remove_avatar" value="1" /><?php $lang('remove_avatar'); ?>
				<br />
			<?php endif; ?>
			<input type="file" name="avatar" id="avatar" />
		</div>
		<p style="clear: both;"><?php $lang('avatar_max_size', array(':size' => $avatar_max_size)); ?></p>
	<?php endif; ?>
	
	<label for="gender"><?php $lang('gender_label'); ?></label>
	<select name="gender" id="gender">
		<option value=""></option>
		<?php foreach($genders as $key => $gender): ?>
			<option value="<?php $esc($key); ?>" <?php if($user->gender == $key) echo 'selected'; ?>><?php $esc($gender); ?></option>
		<?php endforeach; ?>
	</select>
	<p>&nbsp;</p>
	
	<label for="about"><?php $lang('bio_label'); ?></label>
	<textarea name="about" id="about"><?php $esc($user->about); ?></textarea>
	<p>&nbsp;</p>
	
	<h2><?php $lang('chat_settings'); ?></h2>
	<label for="enable_sounds"><?php $lang('enable_sounds'); ?></label>
	<input type="checkbox" name="enable_sounds" id="enable_sounds" value="1" <?php if($user->enable_sounds) echo 'checked'; ?>>
	<p><?php $lang('enable_sounds_instr'); ?></p>
	
	<label for="timestamp_format"><?php $lang('timestamp_format'); ?></label>
	<div class="multi_checkbox" id="timestamp_settings">
		<input type="checkbox" name="use_default_timestamp_settings" id="use_default_timestamp_settings" value="1" <?php if($user->use_default_timestamp_settings) echo 'checked'; ?>> <label for="use_default_timestamp_settings"><?php $lang('use_default_timestamp_settings'); ?></label>
		<br />
		<input type="checkbox" name="enable_timestamps" id="enable_timestamps" value="1" <?php if($user->enable_timestamps) echo 'checked'; ?>> <label for="enable_timestamps"><?php $lang('enable_timestamps'); ?></label>
		<br />
		<input type="checkbox" name="ts_24_hour" id="ts_24_hour" value="1" <?php if($user->ts_24_hour) echo 'checked'; ?>> <label for="ts_24_hour"><?php $lang('ts_24_hour'); ?></label>
		<br />
		<input type="checkbox" name="ts_show_seconds" id="ts_show_seconds" value="1" <?php if($user->ts_show_seconds) echo 'checked'; ?>> <label for="ts_show_seconds"><?php $lang('ts_show_seconds'); ?></label>
		<br />
		<input type="checkbox" name="ts_show_ampm" id="ts_show_ampm" value="1" <?php if($user->ts_show_ampm) echo 'checked'; ?>> <label for="ts_show_ampm"><?php $lang('ts_show_ampm'); ?></label>
		<!--
		<br />
		<input type="checkbox" name="ts_show_date" id="ts_show_date" value="1" <?php if($user->ts_show_date) echo 'checked'; ?>> <label for="ts_show_date"><?php $lang('ts_show_date'); ?></label>
		-->
	</div>
	<p>&nbsp;</p>
	
	<label for="enable_styles"><?php $lang('show_message_styles'); ?></label>
	<input type="checkbox" name="enable_styles" id="enable_styles" value="1" <?php if($user->enable_styles) echo 'checked'; ?>>
	<p><?php $lang('show_message_styles_instr'); ?></p>
	
	<label for="message_font_size"><?php $lang('message_font_size'); ?></label>
	<input type="text" name="message_font_size" id="message_font_size" value="<?php $esc($user->message_font_size); ?>" />
	<p><?php $lang('message_font_size_instr'); ?></p>
	
	<label for="message_font_color"><?php $lang('message_font_color'); ?></label>
	<input type="text" name="message_font_color" id="message_font_color" value="<?php $esc($user->message_font_color); ?>" />
	<p><?php $lang('message_font_color_instr'); ?></p>
	
	<label for="message_font_face"><?php $lang('message_font_face'); ?></label>
	<select name="message_font_face" id="message_font_face">
		<option value=""><?php $lang('default'); ?></option>
		<?php foreach($fonts as $font): ?>
			<option value="<?php $esc($font['id']); ?>" <?php if($user->message_font_face == $font['id']) echo 'selected'; ?>><?php $esc($font['name']); ?></option>
		<?php endforeach; ?>
	</select>
	<p>&nbsp;</p>
	
	<input type="submit" value="<?php $lang('save_settings_button'); ?>" />
</form>
<iframe src="blank.html" id="submit_iframe" name="submit_iframe" height="1" style="visibility: hidden;"></iframe>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript">
	function update_timestamp_settings()
	{
		var use_default_timestamp_settings = $("#use_default_timestamp_settings").attr('checked');
		var ts_24_hour = $("#ts_24_hour").attr('checked');
		var enable_timestamps = $("#enable_timestamps").attr('checked');
		
		$("#timestamp_settings input").attr('disabled', true);
		
		$("#use_default_timestamp_settings").attr('disabled', false);
		if(!use_default_timestamp_settings)
		{
			$("#enable_timestamps").attr('disabled', false);
			
			if(enable_timestamps)
			{
				$("#ts_24_hour").attr('disabled', false);
				$("#ts_show_seconds").attr('disabled', false);
				$("#ts_show_date").attr('disabled', false);
				
				if(!ts_24_hour)
				{
					$("#ts_show_ampm").attr('disabled', false);
				}
			}
		}
	}
	
	$("#timestamp_settings input").bind('click', function() {
		setTimeout(update_timestamp_settings, 250);
	});

	var pickers = new jscolor.color($("#message_font_color")[0], {
		required: false
	});
	
	setTimeout(update_timestamp_settings, 250);
</script>