<?php

	namespace x7;
	
	$ses->check_bans();
	
	if($x7->system_config('disable_account_management'))
	{
		$ses->set_message($x7->lang('feature_disabled'));
		$req->go('login');
	}
	
	$vdata = array(
		'defaults' => $ses->get_flash('forward'),
	);
	
	$x7->display('pages/register', $vdata);