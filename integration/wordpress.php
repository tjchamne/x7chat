<?php

	namespace x7;
	
	require_once(dirname(__FILE__) . '/base.php');

	class wordpress extends integration_base
	{
		public function get_current_user_id()
		{
			$user = wp_get_current_user();
			if($user)
			{
				return $user->ID;
			}
			return 0;
		}
	
		protected function get_user_data_by_id($user_id)
		{
			$user = get_userdata($user_id);
			if(!$user || is_wp_error($user))
			{
				return null;
			}
			else
			{
				return $this->format_user_details($user);
			}
		}
		
		protected function get_user_data_by_username($username)
		{
			$user = get_user_by('login', $username);
			if(!$user || is_wp_error($user))
			{
				return null;
			}
			else
			{
				return $this->format_user_details($user);
			}
		}
		
		protected function authenticate($username, $password)
		{
			$user = wp_authenticate($username, $password);
			if(!$user || is_wp_error($user))
			{
				return null;
			}
			else
			{
				return $user->ID;
			}
		}
		
		protected function format_user_details($user)
		{
			$output = array(
				'id' => $user->ID,
				'username' => $user->display_name,
				'email' => $user->user_email,
				'admin' => user_can($user, 'manage_options'),
			);
			
			return $output;
		}
	}