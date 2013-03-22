<?php

	namespace x7;
	
	$vdata = array(
		'news' => $x7->config('login_page_news'),
		'defaults' => $ses->get_flash('forward'),
	);

	$x7->display('pages/login', $vdata);