<div id="title_def"><?php $lang('logs_title'); ?></div>
<form data-action="logs">
	<input type="hidden" name="type" value="room" />
	<input type="hidden" name="id" value="<?php $esc($id); ?>" />
	
	<label for="start_date"><?php $lang('start_date_label'); ?></label>
	<input type="text" name="start_date" id="start_date" value="<?php $var('defaults.start_date'); ?>" />
	
	<label for="end_date"><?php $lang('end_date_label'); ?></label>
	<input type="text" name="end_date" id="end_date" value="<?php $var('defaults.end_date'); ?>" />
	
	<label for="log_view_mode"><?php $lang('log_view_mode'); ?></label>
	<br />
	<select name="log_view_mode" id="log_view_mode">
		<option value="show"><?php $lang('show_logs'); ?></option>
		<option value="download"><?php $lang('download_logs'); ?></option>
	</select>
	
	<br /><br />
	<input type="submit" value="<?php $lang('get_logs'); ?>" />
</form>
<?php if($logs): ?>
	<hr />
	<pre><?php $display('components/messages'); ?></pre>
	<script type="text/javascript">
		$(function() {
			$("#start_date,#end_date").datetimepicker({
				 language: '<?php echo $esc($x7->lang('datetimepicker_lang')); ?>',
			});
		});
	</script>
<?php endif; ?>