<form class="standard_form" data-action="do_admin_save_smiley">
	<?php if($val('smiley.id')): ?>
		<input type="hidden" name="id" value="<?php $esc($val('smiley.id')); ?>" />
	<?php endif; ?>

	<label for="token"><?php $lang('smiley_token'); ?></label>
	<input type="text" name="token" id="token" value="<?php $esc($val('smiley.token')); ?>" />
	<p><?php $lang('smiley_token_instr'); ?></p>
	
	<label for="image"><?php $lang('smiley_image'); ?></label>
	<br />
	<?php if($val('smiley.image')): ?>
		<input type="radio" name="image" value="<?php echo $esc($var('smiley.image')); ?>" checked="checked" /> <img src="<?php $var('smiley.image'); ?>" /><br />
	<?php endif; ?>
	<?php foreach($smilies as $path => $in_use): ?>
		<?php if(!$in_use): ?>
			<input type="radio" name="image" value="<?php echo $esc($path); ?>" /> <img src="<?php echo $path; ?>" /><br />
		<?php endif; ?>
	<?php endforeach; ?>
	
	<p><?php $lang('smiley_image_instr'); ?></p>
	
	<input type="submit" value="<?php $lang('smiley_save_button'); ?>" />
</form>