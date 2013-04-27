<?php

	namespace x7;
	
	$vdata = array(
		'news' => $x7->config('login_page_news'),
		'defaults' => $ses->get_flash('forward'),
		'can_create_account' => !$x7->system_config('disable_account_management'),
		'can_reset_password' => !$x7->system_config('disable_account_management'),
	);

	$x7->display('pages/login', $vdata);