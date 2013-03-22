<?php

	namespace x7\integration\standalone;
	
	use x7\exception,
		x7\model;
	
	class authenticator
	{
		protected $x7;
		
		public function __construct($x7)
		{
			$this->x7 = $x7;
		}
		
		public function disable_registration()
		{
			return false;
		}
		
		public function disable_password_reset()
		{
			return false;
		}
		
		protected function phpass()
		{
			require_once($this->x7->root . 'includes/libraries/phpass/PasswordHash.php');
			return new \PasswordHash(8, false);
		}
		
		public function hash_password($password)
		{
			$phpass = $this->phpass();
			return $phpass->HashPassword($password);
		}
		
		public function login_user($username, $password)
		{
			try
			{
				$user = $this->authenticate($username, $password);
			}
			catch(exception\nonexistent_username $ex)
			{
				if(!$this->x7->config('allow_guests'))
				{
					throw new exception\authentication_exception;
				}
				
				$user = new model\user(array('username' => $username));
				$users = $this->x7->users();
				$users->create_guest($user);
			}
			
			$this->x7->session()->current_user($user);
		}
		
		public function authenticate($username, $password)
		{
			$users = $this->x7->users();
			$users->cleanup_guests();
		
			$user = $users->load_by_username($username);
			$phpass = $this->phpass();
			if(!$phpass->CheckPassword($password, $user->password))
			{
				throw new exception\authentication_exception;
			}
			
			return $user;
		}
	}