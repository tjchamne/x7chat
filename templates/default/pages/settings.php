<div id="title_def"><?php $lang('settings_title'); ?></div>
<?php $display('layout/messages'); ?>
<?php $display('components/user_form'); ?>
<script type="text/javascript">
	App.user = <?php echo json_encode($user); ?>;
</script>