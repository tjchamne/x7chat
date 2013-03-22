<?php

	namespace x7;
	
	class wordpress
	{
		protected $root;
		protected $api;
	
		public function __construct($root)
		{
			$this->root = $root;
		}
		
		public function generate_session_key()
		{
			$api = $this->get_api(); // this triggers a load of deps
			$response = new \x7\model\api_message;
			
			$user = wp_get_current_user();
			if($user->ID == 0)
			{
				return '';
			}
			$response->id = $user->ID;
			
			return $this->create_message($response);
		}
		
		public function handle_message()
		{
			$response = null;
			$message = $this->get_message();
			if($message)
			{
				if($message->method == 'authenticate')
				{
					$response = $this->handle_authenticate($message);
				}
				elseif($message->method == 'user_details')
				{
					$response = $this->handle_user_details($message);
				}
			}
			
			if($response)
			{
				echo $this->create_message($response);			
			}
		}
		
		protected function handle_user_details($msg)
		{
			$response = new \x7\model\api_message;
			
			$user = get_userdata($msg->id);
			if(!$user || is_wp_error($user))
			{
				$response->ok = false;
			}
			else
			{
				$response->ok = true;
				$response->user = $this->format_user_details($user);
			}
			
			return $response;
		}
		
		protected function handle_authenticate($msg)
		{
			$response = new \x7\model\api_message;
			
			$user = wp_authenticate($msg->username, $msg->password);
			if(!$user || is_wp_error($user))
			{
				$response->ok = false;
				$user = get_user_by('login', $msg->username);
				if($user && !is_wp_error($user))
				{
					$response->user = $user;
				}
			}
			else
			{
				$response->ok = true;
				$response->user = $this->format_user_details($user);
			}
			
			return $response;
		}
		
		protected function format_user_details($user)
		{
			$output = array(
				'id' => $user->ID,
				'username' => $user->display_name,
				'email' => $user->user_email,
			);
			
			return $output;
		}
		
		public function get_api()
		{
			if(!$api && !class_exists('\\x7\\api'))
			{
				require_once($this->root . 'includes/model.php');
				require_once($this->root . 'includes/model/api_message.php');
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/Hash.php');
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/AES.php');
				require_once($this->root . 'includes/api.php');
			}
			
			return new api($this->get_api_key());
		}
		
		public function get_api_key()
		{
			$config = require($this->root . 'config.php');
			return isset($config['api_key']) ? $config['api_key'] : '';
		}
		
		public function get_message()
		{
			return $this->get_api()->get_message(isset($_POST['message']) ? $_POST['message'] : '');
		}
		
		public function create_message(model\api_message $message)
		{
			return $this->get_api()->create_message($message);
		}
	}