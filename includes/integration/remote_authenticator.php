<?php

	namespace x7\integration;
	
	use x7\exception,
		x7\model;
	
	abstract class remote_authenticator extends \x7\integration\standalone\authenticator
	{
		public function hash_password($password)
		{
			return 'x';
		}
		
		public function disable_registration()
		{
			return true;
		}
		
		public function disable_password_reset()
		{
			return true;
		}
		
		public function login_user_by_id($id)
		{
			$message = new model\api_message(array(
				'method' => 'user_details',
				'id' => $id,
			));
			
			$response = $this->send_request($message);
			if($response && $response->ok)
			{
				$user = $this->sync_user($response->user);
				$this->x7->session()->current_user($user);
			}
			else
			{
				throw new exception\authentication_exception;
			}
		}
	
		public function login_user($username, $password)
		{
			$response = $this->remote_auth($username, $password);
			
			if($response->ok)
			{
				$user = $this->sync_user($response->user);
				$this->x7->session()->current_user($user);
			}
			else
			{
				parent::login_user($username, $password);
			}
		}
		
		protected function remote_auth($username, $password)
		{
			$message = new model\api_message(array(
				'method' => 'authenticate',
				'username' => $username,
				'password' => $password,
			));
			
			$response = $this->send_request($message);
			if(!$response)
			{
				throw new exception\authentication_exception;
			}
			
			if(!$response->ok && !empty($response->user))
			{
				throw new exception\authentication_exception;
			}
			
			return $response;
		}
		
		public function authenticate($username, $password)
		{
			$response = $this->remote_auth($username, $password);
			if($response->ok)
			{
				$user = $this->sync_user($response->user);
			}
			else
			{
				$user = parent::authenticate($username, $password);
			}
			
			return $user;
		}
		
		public function send_request(model\api_message $message)
		{
			$url = $this->get_endpoint_url();
			$api = $this->x7->api();
			$message = $api->create_message($message);
			
			$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query(array('message' => $message)),
					'timeout' => 10,
				)
			);
			$context = stream_context_create($opts);
			$result = @file_get_contents($url, false, $context);
			
			$response = $api->get_message($result);
			return $response;
		}
		
		public function get_endpoint_url()
		{
			$endpoint = $this->x7->system_config('auth_api_endpoint');
			if(!$endpoint)
			{
				$endpoint = $this->get_url();
			}
			return $endpoint;
		}
		
		public function sync_user($details)
		{
			try
			{
				$user = $this->x7->users()->load_by_username($details['username']);
			}
			catch(exception\nonexistent_username $ex)
			{
				$user = new model\user;
			}
		
			$user->username = $details['username'];
			$user->email = $details['email'];
			$user->password = 'x';
			$user = $this->x7->users()->save_user($user);
			
			return $user;
		}
		
		abstract public function get_url();
	}