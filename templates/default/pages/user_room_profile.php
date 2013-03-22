<div id="title_def"><?php $esc($user->username); ?></div>
<br />
<?php $display('layout/messages'); ?>
<?php if($user->avatar): ?>
	<a href="uploads/<?php echo $user->avatar; ?>" target="_blank"><img src="uploads/<?php echo 'normal_' . $user->avatar; ?>" /></a>
<?php endif; ?>

<?php if($user->real_name): ?>
	<p><b><?php $lang('real_name_label'); ?></b></p>
	<p><?php $esc($user->real_name); ?></p>
	<hr />
<?php endif; ?>
<?php if($user->gender): ?>
	<p><b><?php $lang('gender_label'); ?></b></p>
	<p><?php $lang($user->gender); ?></p>
	<hr />
<?php endif; ?>
<?php if($user->about): ?>
	<p><b><?php $lang('bio_label'); ?></b></p>
	<p><?php $esc($user->about); ?></p>
	<hr />
<?php endif; ?>
<?php if($user->location): ?>
	<p><b><?php $lang('location_label'); ?></b></p>
	<p><?php $esc($user->location); ?></p>
	<hr />
<?php endif; ?>
<?php if($user->status_description): ?>
	<p><b><?php $lang('status_label'); ?></b></p>
	<p><?php $esc($user->status_description); ?> (<?php $user->status_type ? $lang($user->status_type . '_option') : $lang('available_option'); ?>)</p>
	<hr />
<?php endif; ?>
<?php if($show_ip && $user->ip): ?>
	<p><b><?php $lang('ip_label'); ?></b></p>
	<p><?php $esc($user->ip); ?><?php if($allow_ban): ?> - <a href="#" id="ip_ban"><?php $lang('ban_by_ip'); ?></a><?php endif; ?></p>
	<hr />
<?php endif; ?>

<p><a href="#" id="start_private_chat">Start private chat</a></p>
<?php if($allow_ban): ?><p><a href="#" id="user_ban"><?php $lang('ban_user'); ?></a></p><?php endif; ?>

<script type="text/javascript">
	$("#start_private_chat").bind('click', function() {
		var room = new App.Room({
			id: '<?php echo $user->id; ?>',
			type: 'user',
			name: <?php echo json_encode($x7->esc($user->username)); ?>
		});
		
		App.add_room(room);
		
		App.set_active_room(room);
		close_content_area();
	});
	
	$("#ip_ban").bind('click', function() {
		open_content_area('<?php $url('ban') ?>&by=ip&user_id=<?php echo $user->id; ?>');
	});
	
	$("#user_ban").bind('click', function() {
		open_content_area('<?php $url('ban') ?>&by=account&user_id=<?php echo $user->id; ?>');
	});
</script>