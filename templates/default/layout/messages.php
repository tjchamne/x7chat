<?php if(!empty($errors)): ?>
	<ul class='errors'>
		<?php foreach($errors as $error): ?>
			<li class='error'><?php $esc($error); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
<?php if(!empty($notices)): ?>
	<ul class='notices'>
		<?php foreach($notices as $notice): ?>
			<li class='notice'><?php $esc($notice); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>