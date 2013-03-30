<div id="title_def"><?php $lang('admin_groups_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<table class="data_table" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php $lang('group_name'); ?></th>
				<th><?php $lang('actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($groups as $group): ?>
				<tr>
					<td><?php $esc($group['name']); ?></td>
					<td>
						<a href="#" data-href="admin_edit_group&id=<?php echo $group['id']; ?>"><?php $lang('edit'); ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php $display('layout/paginator', array('data' => $paginator)); ?>
</div>