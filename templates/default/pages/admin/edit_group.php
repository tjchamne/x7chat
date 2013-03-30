<div id="title_def"><?php $lang('admin_groups_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<form class="standard_form" data-action="do_admin_edit_group">
		<input type="hidden" name="id" id="id" value="<?php $esc($val('group.id')); ?>" />

		<label for="name"><?php $lang('group_name'); ?></label>
		<input type="text" name="name" id="name" value="<?php $esc($val('group.name')); ?>" />
		<p>&nbsp;</p>
		
		<label for="access_admin_panel"><?php $lang('perm_access_admin_panel'); ?></label>
		<input type="checkbox" name="access_admin_panel" id="access_admin_panel" <?php if($val('group.access_admin_panel')) echo "checked"; ?> />
		<p><?php $lang('perm_access_admin_panel_desc'); ?></p>
		
		<label for="create_room"><?php $lang('perm_create_room'); ?></label>
		<input type="checkbox" name="create_room" id="create_room" <?php if($val('group.create_room')) echo "checked"; ?> />
		<p><?php $lang('perm_create_room_desc'); ?></p>
		
		<label for="view_logs"><?php $lang('perm_view_logs'); ?></label>
		<input type="checkbox" name="view_logs" id="view_logs" <?php if($val('group.view_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_logs_desc'); ?></p>
		
		<label for="view_unrestricted_logs"><?php $lang('perm_view_unrestricted_logs'); ?></label>
		<input type="checkbox" name="view_unrestricted_logs" id="view_unrestricted_logs" <?php if($val('group.view_unrestricted_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_unrestricted_logs_desc'); ?></p>
		
		<label for="view_private_logs"><?php $lang('perm_view_private_logs'); ?></label>
		<input type="checkbox" name="view_private_logs" id="view_private_logs" <?php if($val('group.view_private_logs')) echo "checked"; ?> />
		<p><?php $lang('perm_view_private_logs_desc'); ?></p>
		
		<input type="submit" value="<?php $lang('save_group_button'); ?>" />
	</form>
</div>