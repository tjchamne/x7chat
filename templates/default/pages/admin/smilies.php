<div id="title_def"><?php $lang('admin_smiley_title'); ?></div>
<?php $display('layout/adminmenu'); ?>
<div id="admin_content">
	<?php $display('layout/messages'); ?>
	<?php $display('layout/paginator', array('data' => isset($paginator) ? $paginator : array())); ?>
	
	<?php if(!$smilies): ?>
		<p><?php $lang('no_smilies_defined'); ?></p>
	<?php else: ?>
		<table class="data_table" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php $lang('smiley_token'); ?></th>
					<th><?php $lang('smiley_image'); ?></th>
					<th><?php $lang('actions'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($smilies as $smiley): ?>
					<tr>
						<td><?php $esc($smiley['token']); ?></td>
						<td><img src="<?php $esc($smiley['image']); ?>" /></td>
						<td>
							<a href="#" data-href="admin_edit_smiley&id=<?php echo $smiley['id']; ?>"><?php $lang('edit'); ?></a> | <a href="#" data-href="admin_delete_smiley&id=<?php echo $smiley['id']; ?>"><?php $lang('delete'); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>