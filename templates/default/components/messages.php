<?php if($logs): ?>
<?php foreach($logs as $log): ?>
<?php
	$before = ($log['timestamp_fmt'] ? $log['timestamp_fmt'] . ' | ' : '');
	$before .= ($log['sender_name'] ? $x7->esc($log['sender_name']) . ': ' : '');
	echo $before;
	
	$padding = "\n" . str_repeat(" ", strlen($before));
	$message = str_replace("\r", '', $log['message']);
	$message = str_replace("\n", $padding, $message);
	$esc($message);
	echo "\n"; ?>
<?php endforeach; ?>
<?php else: ?>
<?php $lang('no_logs'); ?>
<?php endif; ?>