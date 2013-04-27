<div id="title_def"><?php $lang('admin_groups_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<form class="standard_form" id="settings_form" enctype="multipart/form-data" target="submit_iframe" method="post" action="<?php echo $url('do_admin_edit_group'); ?>">
		<input type="hidden" name="id" id="id" value="<?php $esc($val('group.id')); ?>" />

		<label for="name"><?php $lang('group_name'); ?></label>
		<input type="text" name="name" id="name" value="<?php $esc($val('group.name')); ?>" />
		<p>&nbsp;</p>
		
		<label for="color"><?php $lang('group_color'); ?></label>
		<input type="text" name="color" id="color" value="<?php $esc($val('group.color')); ?>" />
		<p><?php $lang('group_color_desc'); ?></p>
		
		<?php if($allow_avatar): ?>
			<label for="avatar"><?php $lang('group_avatar'); ?></label>
			<div style="display: inline-block;">
				<?php if($val('group.image')): ?>
					<img src="uploads/normal_<?php echo $val('group.image'); ?>" />
					<br />
					<input type="checkbox" name="remove_avatar" value="1" /><?php $lang('remove_avatar'); ?>
					<br />
				<?php endif; ?>
				<input type="file" name="avatar" id="avatar" />
			</div>
			<p style="clear: both;"><?php $lang('avatar_max_size', array(':size' => $avatar_max_size)); ?></p>
		<?php endif; ?>
		
		<label for="access_admin_panel"><?php $lang('perm_access_admin_panel'); ?></label>
		<input type="checkbox" name="access_admin_panel" id="access_admin_panel" <?php if($val('group.access_admin_panel')) echo "checked"; ?> />
		<p><?php $lang('perm_access_admin_panel_desc'); ?></p>
		
		<!--
		<label for="create_room"><?php $lang('perm_create_room'); ?></label>
		<input type="checkbox" name="create_room" id="create_room" <?php if($val('group.create_room')) echo "checked"; ?> />
		<p><?php $lang('perm_create_room_desc'); ?></p>
		-->
		
		<label for="view_logs"><?php $lang('perm_view_logs'); ?></label>
		<input type="checkbox" name="view_logs" id="view_logs" <?php if($val('group.view_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_logs_desc'); ?></p>
		
		<label for="view_unrestricted_logs"><?php $lang('perm_view_unrestricted_logs'); ?></label>
		<input type="checkbox" name="view_unrestricted_logs" id="view_unrestricted_logs" <?php if($val('group.view_unrestricted_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_unrestricted_logs_desc'); ?></p>
		
		<!--
		<label for="view_private_logs"><?php $lang('perm_view_private_logs'); ?></label>
		<input type="checkbox" name="view_private_logs" id="view_private_logs" <?php if($val('group.view_private_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_private_logs_desc'); ?></p>
		-->
		
		<label for="ban_users"><?php $lang('perm_ban_users'); ?></label>
		<input type="checkbox" name="ban_users" id="ban_users" <?php if($val('group.ban_users')) echo "checked"; ?> />
		<p><?php $lang('perm_ban_users_desc'); ?></p>
		
		<input type="submit" value="<?php $lang('save_group_button'); ?>" />
	</form>
	<iframe src="blank.html" id="submit_iframe" name="submit_iframe" height="1" style="visibility: hidden;"></iframe>
</div>
<script type="text/javascript" src="scripts/jscolor/jscolor.js"></script>
<script type="text/javascript">
	new jscolor.color($("#color")[0], {
		required: false
	});
</script>