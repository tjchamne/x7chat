<?php

	namespace x7;
	
	$user = $ses->current_user();
	$req->require_permission('access_admin_panel');
	$ses->check_bans();

	$admin = $x7->admin();
	
	$installed_version = x7chat::VERSION;
	$cur_stable = '';
	$cur_unstable = '';
	$news = array();
	
	$timeout = array('http' => array('timeout' => 3));
	$context = stream_context_create($timeout);
	$updates = @file_get_contents('http://www.x7chat.com/updates/v3.php', false, $context);
	if($updates)
	{
		$updates = json_decode($updates);
		$cur_stable = $updates->stable_release;
		$cur_unstable = $updates->unstable_release;
		$news = $updates->news;
	}
	
	$x7->display('pages/admin/news', array(
		'installed_version' => $installed_version,
		'cur_stable' => $cur_stable,
		'cur_unstable' => $cur_unstable,
		'news' => $news,
		'menu' => $admin->generate_admin_menu('news'),
	));