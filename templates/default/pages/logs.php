<div id="title_def"><?php $lang('logs_title'); ?></div>
<?php if($logs): ?>
	<?php foreach($logs as $log): ?>
		<?php echo $log['timestamp']; ?>: <?php echo $esc($log['message']); ?><br />
	<?php endforeach; ?>
<?php else: ?>
	<p><?php $lang('no_logs'); ?></p>
<?php endif; ?>