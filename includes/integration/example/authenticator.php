<?php

	namespace x7\integration\EXAMPLE;
	
	use x7\exception,
		x7\model;
	
	class authenticator extends \x7\integration\remote_authenticator
	{
		public function get_url()
		{
			return 'http://EXAMPLE.com/EXAMPLE.php';
		}
	}