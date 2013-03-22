<?php

	namespace x7;
	
	class request
	{
		protected $x7;
		protected $is_ajax_request;
		
		public function __construct($x7 = null)
		{
			if($x7 === null)
			{
				global $x7;
			}
			
			$this->x7 = $x7;
			$this->is_ajax_request(false);
		}
		
		public function is_ajax_request($set = null)
		{
			if($set !== null)
			{
				$this->is_ajax_request = $set;
			}
			
			return $this->is_ajax_request;
		}
		
		public function require_permission($permission)
		{
			$users = $this->x7->users();
			$user = $this->x7->session()->current_user();
			if(!$users->has_permission($user, $permission))
			{
				throw new exception\permission_denied;
			}
		}
		
		public function post($var)
		{
			return isset($_POST[$var]) ? $_POST[$var] : null;
		}
		
		public function go($to, $forward = false)
		{
			if($forward)
			{
				$ses = $this->x7->session();
				
				if($forward === true)
				{
					$forward = $_POST;
				}
				
				$ses->set_flash('forward', $forward);
			}
			
			$to = preg_replace("#^([a-z0-9_-]+)\?#i", '$1&', $to);
			
			if($this->is_ajax_request())
			{
				header("Content-Type: text/json");
				echo json_encode(array('redirect' => '?page=' . $to));
			}
			else
			{			
				header("Location: ?page=$to");
			}
			
			session_write_close();
			exit;
		}
	}