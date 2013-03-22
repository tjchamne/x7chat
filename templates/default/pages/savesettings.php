<script type="text/javascript">
	<?php if($is_admin): ?>
		window.parent.open_content_area('<?php $url('admin_edit_user?id=' . $user->id); ?>');
	<?php else: ?>
		window.parent.open_content_area('<?php $url('settings'); ?>');
	<?php endif; ?>
</script>