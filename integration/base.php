<?php

	namespace x7;
	
	abstract class integration_base
	{
		protected $root;
		protected $api;
		
		/**
		 * Must return the numeric integer ID of the user who is currently logged in.
		 * If no user is logged in, return a false-ish value (0, false, '', etc.)
		 */
		abstract protected function get_current_user_id();
		
		/**
		 * Must return an array of data for the specified user, or null if the user
		 * id is not valid.  The array must have three keys:
		 *     'id' => the ID of the user
		 *     'username' => the username of the user
		 *     'email' => the E-Mail address of the user
		 */
		abstract protected function get_user_data_by_id($user_id);
		
		/**
		 * Must return an array of data for the specified user, or null if the user
		 * username is not valid.  The array must have three keys:
		 *     'id' => the ID of the user
		 *     'username' => the username of the user
		 *     'email' => the E-Mail address of the user
		 */
		abstract protected function get_user_data_by_username($username);
		
		/**
		 * Must verify that the given username and password are correct.  If they
		 * are not, return null.  If they are, return the user ID of the user.
		 */
		abstract protected function authenticate($username, $password);
	
		/**
		 * $root needs to be the absolute path to the x7chat directory, 
		 * ending in a trailing slash
		 */
		public function __construct($root)
		{
			$this->root = $root;
		}
		
		/**
		 * Call this function to obtain a session key for logging in a user
		 */
		public function generate_session_key()
		{
			$api = $this->get_api(); // this triggers a load of deps
			$response = new \x7\model\api_message;
			$user_id = $this->get_current_user_id();
			if(!$user_id)
			{
				return '';
			}
			$response->id = $user_id;
			return $this->create_message($response);
		}
		
		/**
		 * Call this function to handle incoming API messages
		 */
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
			
			$user = $this->get_user_data_by_id($msg->id);
			if(!$user)
			{
				$response->ok = false;
			}
			else
			{
				$response->ok = true;
				$response->user = $user;
			}
			
			return $response;
		}
		
		protected function handle_authenticate($msg)
		{
			$response = new \x7\model\api_message;
			
			$user = null;
			
			$user_id = $this->authenticate($msg->username, $msg->password);
			if($user_id)
			{
				$user = $this->get_user_data_by_id($user_id);
			}
			
			if(!$user)
			{
				$response->ok = false;
				$user = $this->get_user_data_by_username($msg->username);
				if($user)
				{
					$response->user = $user;
				}
			}
			else
			{
				$response->ok = true;
				$response->user = $user;
			}
			
			return $response;
		}
		
		protected function get_api()
		{
			if(!$this->api && !class_exists('\\x7\\api'))
			{
				require_once($this->root . 'includes/model.php');
				require_once($this->root . 'includes/model/api_message.php');
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/Hash.php');
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/AES.php');
				require_once($this->root . 'includes/api.php');
				
				$this->api = new api($this->get_api_key());
			}
			
			return $this->api;
		}
		
		protected function get_api_key()
		{
			$config = require($this->root . 'config.php');
			if(!empty($config['auth_plugin']))
			{
				$ext_config = require($this->root . 'includes/integration/' . $config['auth_plugin'] . '/config_loader.php');
			}
			$config = array_merge($config, $ext_config);
			return isset($config['api_key']) ? $config['api_key'] : '';
		}
		
		protected function get_message()
		{
			return $this->get_api()->get_message(isset($_POST['message']) ? $_POST['message'] : '');
		}
		
		protected function create_message(model\api_message $message)
		{
			return $this->get_api()->create_message($message);
		}
	}