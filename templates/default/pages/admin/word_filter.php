<div id="title_def"><?php $lang('admin_word_filter_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<?php $display('layout/paginator', array('data' => $paginator)); ?>
	
	<?php if(!$filters): ?>
		<p><?php $lang('no_word_filters_defined'); ?></p>
	<?php else: ?>
		<table class="data_table" cellspacing="0" cellpadding="0">
			<tr>
				<th><?php $lang('word_filter_word'); ?></th>
				<th><?php $lang('word_filter_replacement'); ?></th>
				<th><?php $lang('word_filter_whole_word'); ?></th>
				<th><?php $lang('actions'); ?></th>
			</tr>
			<?php foreach($filters as $filter): ?>
				<tr>
					<td><?php $esc($filter['word']); ?></td>
					<td><?php $esc($filter['replacement']); ?></td>
					<td><?php $filter['whole_word_only'] ? $lang('yes') : $lang('no'); ?></td>
					<td>
						<a href="#" data-href="admin_edit_filter&id=<?php echo $filter['id']; ?>"><?php $lang('edit'); ?></a> | <a href="#" data-href="admin_delete_filter&id=<?php echo $filter['id']; ?>"><?php $lang('delete'); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>