<?php

	namespace x7;
	
	$ses->check_bans();
	
	$vdata = array(
		'defaults' => $ses->get_flash('forward'),
	);
	
	$x7->display('pages/register', $vdata);