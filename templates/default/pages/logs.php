<div id="title_def"><?php $lang('logs_title'); ?></div>
<form>
	<label for="search_text"><?php $lang('search_text_label'); ?></label>
	<input type="text" name="search_text" id="search_text" value="<?php $var('defaults.search_text'); ?>" />
	
	<label for="start_date"><?php $lang('start_date_label'); ?></label>
	<input type="text" name="start_date" id="start_date" value="<?php $var('defaults.start_date'); ?>" />
	
	<label for="end_date"><?php $lang('end_date_label'); ?></label>
	<input type="text" name="end_date" id="end_date" value="<?php $var('defaults.end_date'); ?>" />
	
	<label for="involved_users"><?php $lang('involved_users_label'); ?></label>
	<br />
	<input style="width: 250px;" type="text" name="involved_users" id="involved_users" value="<?php $var('defaults.involved_users'); ?>" />
	<br />
	<br />
	
	<label for="involved_rooms"><?php $lang('involved_rooms_label'); ?></label>
	<input type="text" name="involved_rooms" id="involved_rooms" value="<?php $var('defaults.involved_rooms'); ?>" />
	
	<label for="message_type"><?php $lang('message_type_label'); ?></label>
	<select name="message_type">
		<option value="public"><?php $lang('public_message'); ?></option>
		<option value="private"><?php $lang('private_message'); ?></option>
	</select>
	
	<input type="submit" name="show_logs" value="<?php $lang('show_logs'); ?>" />
	<input type="submit" name="download_logs" value="<?php $lang('download_logs'); ?>" />
</form>
<script type="text/javascript">
	var autocomp_xhr;

	$("#involved_users").select2({
		no_results_text: "<?php $lang('no_results'); ?>", 
		placeholder_text: "<?php $lang('select_an_option'); ?>",
		query: function(query) {
			if(autocomp_xhr)
			{
				autocomp_xhr.abort();
			}
			
			autocomp_xhr = $.ajax({
				url: '<?php $url('logs_suggest_users_ajax'); ?>',
				cache: false,
				type: 'POST',
				dataType: 'json',
				data: {
					query: query.term
				},
				success: query.callback
			});
		}
	});
</script>

<?php var_export($rooms); ?>
<hr />
<pre><?php $display('components/messages'); ?></pre>