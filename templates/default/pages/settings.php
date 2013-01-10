<div id="title_def"><?php $lang('settings_title'); ?></div>
<?php $display('layout/messages'); ?>
<form class="standard_form" id="settings_form">
	<h2><?php $lang('account_settings'); ?></h2>
	<label for="current_password"><?php $lang('current_password'); ?></label>
	<input type="password" name="current_password" id="current_password" />
	<p><?php $lang('current_password_instr'); ?></p>
	
	<label for="new_password"><?php $lang('new_password'); ?></label>
	<input type="password" name="new_password" id="new_password" />
	<p><?php $lang('new_password_instr'); ?></p>
	
	<label for="retype_new_password"><?php $lang('retype_new_password'); ?></label>
	<input type="password" name="retype_new_password" id="retype_new_password" />
	<p><?php $lang('retype_new_password_instr'); ?></p>
	
	<label for="email"><?php $lang('email_label'); ?></label>
	<input type="text" name="email" id="email" value="<?php $esc($user['email']); ?>" />
	<p>&nbsp;</p>
	
	<h2><?php $lang('profile_settings'); ?></h2>
	<label for="real_name"><?php $lang('real_name_label'); ?></label>
	<input type="text" name="real_name" id="real_name" value="<?php $esc($user['real_name']); ?>" />
	<p>&nbsp;</p>
	
	<label for="gender"><?php $lang('gender_label'); ?></label>
	<select name="gender" id="gender">
		<option value=""></option>
		<?php foreach($genders as $key => $gender): ?>
			<option value="<?php $esc($key); ?>" <?php if($user['gender'] == $key) echo 'selected'; ?>><?php $esc($gender); ?></option>
		<?php endforeach; ?>
	</select>
	<p>&nbsp;</p>
	
	<label for="bio"><?php $lang('bio_label'); ?></label>
	<textarea name="bio" id="bio"><?php $esc($user['about']); ?></textarea>
	<p>&nbsp;</p>
	
	<input type="submit" value="<?php $lang('save_settings_button'); ?>" />
</form>
<script type="text/javascript">
	$("#settings_form").bind('submit', function() {
		$.post('<?php $url('savesettings'); ?>', $(this).serialize(), function(data) {
			$('#content_page').html(data);
			$('#content_page').scrollTop(0);
		});
		return false;
	});
</script>