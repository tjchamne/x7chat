<?php

	class integration
	{
		const LOGIN_OK = 'valid';
		const LOGIN_FAILURE = 'invalid';
		const LOGIN_LOCAL_ERROR = 'local_error';
		
		protected $x7;
	
		public function __construct($x7)
		{
			$this->x7 = $x7;
		}
		
		public function login_user($username, $password)
		{
			// look up user in db
			// if not found or expired:
				// if guests are enabled: login as guest
				// if guests are disabled: login failure
			// if found and valid: login user
			// if found and not valid: login failure
		}
		
		protected function force_login_user($username)
		{
		
		}
		
		public function register_link()
		{
			return true;
		}
		
		public function password_reset_link()
		{
			return true;
		}
		
		public function disable_account_settings()
		{
			return true;
		}
	}