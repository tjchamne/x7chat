<?php

	namespace x7;
	
	class session
	{
		protected $x7;
		protected $db;
		protected $dbprefix;
		
		public function __construct($x7 = null)
		{
			if($x7 === null)
			{
				global $x7;
			}
			
			$this->x7 = $x7;
			$this->db = $x7->db();
			$this->dbprefix = $x7->dbprefix;
			
			session_start();
		}
		
		public function handle_key($key)
		{
			try
			{
				$this->current_user();
			}
			catch(exception\user_not_authenticated $ex)
			{
				$api = $this->x7->api();
				$msg = $api->get_message($key);
				try
				{
					$auth = $this->x7->auth();
					$auth->login_user_by_id($msg->id);
				}
				catch(exception\authentication_exception $ex)
				{
					// ignore
				}
			}
		}
		
		public function get_flash($key, $clear = true)
		{
			if(isset($_SESSION['flash'][$key]))
			{
				$value = $_SESSION['flash'][$key];
				
				if($clear)
				{
					unset($_SESSION['flash'][$key]);
				}
				
				return $value;
			}
			
			return null;
		}
		
		public function set_flash($key, $value)
		{
			if(empty($_SESSION['flash']))
			{
				$_SESSION['flash'] = array();
			}
			
			$_SESSION['flash'][$key] = $value;
			
			return $value;
		}
		
		public function set_message($message, $type = 'error')
		{
			$_SESSION['messages'][$type][] = $message;
		}
		
		public function get_messages($type, $clear = true)
		{
			$messages = isset($_SESSION['messages'][$type]) ? $_SESSION['messages'][$type] : array();
		
			if($clear)
			{
				unset($_SESSION['messages'][$type]);
			}
			
			return $messages;
		}
		
		public function check_bans()
		{
			if(!empty($_SESSION['banned']))
			{
				throw $_SESSION['banned'];
			}
		
			$bans = $this->x7->bans();
		
			try
			{
				try
				{
					$users = $this->x7->users();
					$user = $users->load_by_id($this->current_user()->id);
					$bans->check_user_ban($user);
				}
				catch(exception\user_not_authenticated $ex)
				{
					// ignore; the user can't be banned by account if they are not logged in
				}
				
				$bans->check_ip_ban($_SERVER['REMOTE_ADDR']);
			}
			catch(exception\user_banned $ex)
			{
				$_SESSION['banned'] = $ex;
				$this->logout();
				throw $ex;
			}
		}
		
		public function current_user($set_user = false)
		{
			if($set_user instanceof model\user)
			{
				$_SESSION['user'] = $set_user;
			}
			elseif($set_user === null)
			{
				$_SESSION = array();
				return true;
			}
		
			if(empty($_SESSION['user']))
			{
				throw new exception\user_not_authenticated;
			}
			
			return $_SESSION['user'];
		}
		
		public function leave_rooms()
		{
			$users = $this->x7->users();
		
			$user = $this->current_user();
			
			$rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
			if(empty($rooms))
			{
				return true;
			}
			
			$users->leave_rooms($user, $rooms);
		}
		
		public function logout()
		{
			$this->leave_rooms();
			$this->current_user(null);
		}
	}