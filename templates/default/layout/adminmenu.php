<ul class="sidemenu">
	<li data-href="admin_news" <?php if($val('page') == 'admin_news') echo "class='active'"; ?>>
		<?php $lang('admin_news_button'); ?>
	</li>
	<li data-href="admin_settings" <?php if($val('page') == 'admin_settings') echo "class='active'"; ?>>
		<?php $lang('admin_settings_button'); ?>
	</li>
	<li data-href="admin_word_filter" <?php if(in_array($val('page'), array('admin_word_filter', 'admin_edit_filter', 'admin_add_filter'))) echo "class='active'"; ?>>
		<?php $lang('admin_word_filter_button'); ?>
		<?php if(in_array($val('page'), array('admin_word_filter', 'admin_add_filter', 'admin_edit_filter'))): ?>
			<ul>
				<li data-href="admin_word_filter" <?php if($val('page') == 'admin_word_filter') echo "class='active'"; ?>><?php $lang('list_filters'); ?></li>
				<li data-href="admin_edit_filter" <?php if($val('page') == 'admin_add_filter') echo "class='active'"; ?>><?php $lang('edit_filter'); ?></li>
			</ul>
		<?php endif; ?>
	</li>
	<li data-href="admin_rooms" <?php if(in_array($val('page'), array('admin_create_room', 'admin_edit_room', 'admin_rooms'))) echo "class='active'"; ?>>
		<?php $lang('admin_rooms_button'); ?>
		<?php if(in_array($val('page'), array('admin_create_room', 'admin_edit_room', 'admin_rooms'))): ?>
			<ul>
				<li data-href="admin_rooms" <?php if($val('page') == 'admin_rooms') echo "class='active'"; ?>><?php $lang('list_rooms'); ?></li>
				<li data-href="admin_edit_room" <?php if($val('page') == 'admin_create_room') echo "class='active'"; ?>><?php $lang('create_room'); ?></li>
			</ul>
		<?php endif; ?>
	</li>
</ul>