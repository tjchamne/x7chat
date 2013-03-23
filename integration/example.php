<?php

	namespace x7;
	
	require_once(dirname(__FILE__) . '/base.php');

	class EXAMPLE extends integration_base
	{
		/**
		 * Must return the numeric integer ID of the user who is currently logged in.
		 * If no user is logged in, return a false-ish value (0, false, '', etc.)
		 */
		public function get_current_user_id()
		{
			$user_logged_in = true;
			$user_id = 5;
			
			if($user_logged_in)
			{
				return $user_id;
			}
			else
			{
				return 0;
			}
		}
		
		/**
		 * Must return an array of data for the specified user, or null if the user
		 * id is not valid.  The array must have three keys:
		 *     'id' => the ID of the user
		 *     'username' => the username of the user
		 *     'email' => the E-Mail address of the user
		 */
		protected function get_user_data_by_id($user_id)
		{
			$user_data = array(
				'id' => 5,
				'username' => 'user5',
				'email' => 'user5@example.com',
			);
			
			if($user_id == $user_data['id'])
			{
				return $user_data;
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Must return an array of data for the specified user, or null if the user
		 * username is not valid.  The array must have three keys:
		 *     'id' => the ID of the user
		 *     'username' => the username of the user
		 *     'email' => the E-Mail address of the user
		 */
		protected function get_user_data_by_username($username)
		{
			$user_data = array(
				'id' => 5,
				'username' => 'user5',
				'email' => 'user5@example.com',
			);
			
			if($user_id == $user_data['username'])
			{
				return $user_data;
			}
			else
			{
				return null;
			}
		}
		
		/**
		 * Must verify that the given username and password are correct.  If they
		 * are not, return null.  If they are, return the user ID of the user.
		 */
		protected function authenticate($username, $password)
		{
			$user_id = 5;
			
			if($username == 'user5' && $password == 'testing')
			{
				return $user_id;
			}
			else
			{
				return 0;
			}
		}
	}