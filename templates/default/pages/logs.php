<div id="title_def"><?php $lang('logs_title'); ?></div>
<form id="logs_form">
	<input type="hidden" name="type" value="room" />
	<input type="hidden" name="id" value="<?php $esc($id); ?>" />
	
	<!--
	<label for="start_date"><?php $lang('start_date_label'); ?></label>
	<input type="text" name="start_date" id="start_date" value="<?php $var('defaults.start_date'); ?>" />
	
	<label for="end_date"><?php $lang('end_date_label'); ?></label>
	<input type="text" name="end_date" id="end_date" value="<?php $var('defaults.end_date'); ?>" />
	-->
	
	<label for="log_view_mode"><?php $lang('log_view_mode'); ?></label>
	<br />
	<select name="log_view_mode" id="log_view_mode">
		<option value="show"><?php $lang('show_logs'); ?></option>
		<option value="download"><?php $lang('download_logs'); ?></option>
	</select>
	
	<br /><br />
	<input type="submit" value="<?php $lang('get_logs'); ?>" />
</form>
<iframe src="blank.html" id="dl_iframe" name="dl_iframe" height="1" style="visibility: hidden;"></iframe>
<?php if($logs): ?>
	<hr />
	<pre><?php $display('components/messages'); ?></pre>
<?php endif; ?>
<script type="text/javascript">
	$("#logs_form").bind('submit', function(ev)
	{
		ev.preventDefault();
		
		if($("#log_view_mode").val() == 'download')
		{
			console.log('index.php?' + $(this).serialize());
			$("#dl_iframe").attr('src', 'index.php?page=logs&' + $(this).serialize());
		}
		else
		{
			open_content_area('?page=logs', $(this).serialize());
		}
		
		return false;
	});
</script>