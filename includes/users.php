<?php

	namespace x7;
	
	class users
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
		}
		
		public function load_by_id($id)
		{
			try
			{
				return $this->load_by('id', (int)$id);
			}
			catch(exception\user_load_failed $ex)
			{
				throw new exception\invalid_user_id($id);
			}
		}
		
		public function load_by_username($username)
		{
			try
			{
				return $this->load_by('username', $username);
			}
			catch(exception\user_load_failed $ex)
			{
				throw new exception\nonexistent_username($username);
			}
		}
		
		public function load_by_email($email)
		{
			try
			{
				return $this->load_by('email', $email);
			}
			catch(exception\user_load_failed $ex)
			{
				throw new exception\nonexistent_user_email($email);
			}
		}
		
		public function load_by_password_token($token)
		{
			try
			{
				return $this->load_by('reset_password', $token);
			}
			catch(exception\user_load_failed $ex)
			{
				throw new exception\nonexistent_user_password_token($token);
			}
		}
		
		public function delete($user)
		{
			$sql = "DELETE FROM {$this->dbprefix}users WHERE id = :id";
			$st = $this->db->prepare($sql);
			$st->execute(array(
				':id' => $user->id,
			));
			
			$sql = "
				INSERT INTO {$this->dbprefix}messages (timestamp, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message_type, :dest_type, :dest_id, :source_type, :source_id)
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(
				':timestamp' => date('Y-m-d H:i:s'), 
				':message_type' => 'logout', 
				':dest_type' => 'user', 
				':dest_id' => $user->id, 
				':source_type' => 'system', 
				':source_id' => 0,
			));
		}
		
		protected function load_by($field, $value, $into = null)
		{
			$sql = "
				SELECT
					*
				FROM {$this->dbprefix}users
				WHERE
					`{$field}` = :key
			";
			$st = $this->db->prepare($sql);
			
			$params = array(
				':key' => $value,
			);
			$st->execute($params);
			
			$result = $st->fetch();
			$st->closeCursor();
			
			if(empty($result))
			{
				throw new exception\user_load_failed;
			}
			
			if(!$into)
			{
				$into = new model\user($result);
			}
			else
			{
				$into->load($result);
			}
			
			return $into;
		}
		
		public function create_guest(model\user $user)
		{
			$username = $user->username;
			if(empty($username))
			{
				throw new exception\username_required;
			}
		
			$sql = "INSERT INTO {$this->dbprefix}users (username, email, timestamp) VALUES (:username, :email, :timestamp)";
			$st = $this->db->prepare($sql);
			$st->execute(array(':username' => $username, ':email' => $username, ':timestamp' => date('Y-m-d H:i:s')));
			$user_id = $this->db->lastInsertId();
			$this->load_by('id', $user_id, $user);
			return $user;
		}
		
		public function save_user(model\user $user, $fields = array())
		{
			$set = array();
			$params = array();
			
			$new = empty($user->id);
			if($new)
			{
				unset($user->id);
			}
			
			foreach($user as $field => $value)
			{
				if($field === 'id' || empty($fields) || in_array($field, $fields))
				{
					if($field !== 'id')
					{
						$set[] = "`$field` = :$field";
					}
					
					$params[':' . $field] = $value;
				}
			}
			
			if($new)
			{
				$sql = "INSERT INTO {$this->dbprefix}users ";
			}
			else
			{
				$sql = "UPDATE {$this->dbprefix}users ";
			}
			
			$sql .= " SET " . implode(', ', $set);
			
			if(!$new)
			{
				$sql .= " WHERE id = :id";
			}
			
			$st = $this->db->prepare($sql);
			$st->execute($params);
			
			if($new)
			{
				$user->id = $this->db->lastInsertId();
			}
			
			return $user;
		}
		
		public function cleanup_guests()
		{
			$guest_expires = time() - 3600;
			$sql = "DELETE FROM {$this->dbprefix}users WHERE timestamp < :timestamp AND password IS NULL";
			$st = $this->db->prepare($sql);
			$st->execute(array(':timestamp' => date('Y-m-d H:i:s')));
		}
		
		public function is_banned($user)
		{
			// return true/false
		}
		
		public function get_permissions($user)
		{
			$sql = "
				SELECT
					`group`.access_admin_panel,
					`group`.create_room
				FROM {$this->dbprefix}groups `group`
				INNER JOIN {$this->dbprefix}users user ON
					user.id = :user_id
					AND user.group_id = group.id
				LIMIT 1;
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(
				':user_id' => $user->id,
			));
			$row = $st->fetch();
			$st->closeCursor();
			return $row;
		}
		
		public function output($user)
		{
			$new_user = clone $user;
			
			try
			{
				$cur_user = $this->x7->session()->current_user();
			}
			catch(exception\authentication_exception $ex)
			{
				foreach($new_user as $field => $value)
				{
					$new_user->$field = null;
				}
				
				return $new_user;
			}
		
			$new_user->password = null;
			$new_user->reset_password = null;
			
			if(isset($user->id) && $cur_user->id != $user->id && !$this->has_permission($cur_user, 'access_admin_panel'))
			{
				$new_user->email = null;
				$new_user->ip = null;
			}
			
			return $new_user;
		}
		
		public function has_permission($user, $permission)
		{
			$permissions = $this->get_permissions($user);
			if(!empty($permissions[$permission]))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function timeout_users()
		{
			$sql = "
				SELECT
					room_user.user_id,
					room_user.room_id,
					user.timestamp
				FROM {$this->dbprefix}room_users AS room_user
				INNER JOIN {$this->dbprefix}users AS user ON
					user.id = room_user.user_id
					AND user.timestamp < :expires
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(':expires' => date('Y-m-d H:i:s', time() - 61)));
			$rows = $st->fetchAll();
			$users = array();
			$times = array();
			foreach($rows as $row)
			{
				$users[$row['user_id']][] = $row['room_id'];
				$times[$row['user_id']] = $row['timestamp'];
			}
			foreach($users as $user_id => $rooms)
			{
				$user = new model\user(array('id' => $user_id));
				$this->leave_rooms($user, $rooms, $times[$user_id]);
			}
		}
		
		public function leave_rooms($user, $room_ids, $left_at = null)
		{
			$user_id = $user->id;
			
			if($left_at === null)
			{
				$left_at = date('Y-m-d H:i:s');
			}
		
			$rooms_str = implode(',', $room_ids);
			$sql = "
				DELETE FROM {$this->dbprefix}room_users
				WHERE
					room_id IN ({$rooms_str})
					AND user_id = :user_id
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(':user_id' => $user_id));
			
			$rooms_str = implode(',', $room_ids);
			$sql = "
				UPDATE {$this->dbprefix}online SET
					part_timestamp = :now
				WHERE
					room_id IN ({$rooms_str})
					AND user_id = :user_id
					AND part_timestamp IS NULL
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(':user_id' => $user_id, ':now' => $left_at));
			
			foreach($room_ids as $room)
			{
				$sql = "
					INSERT INTO {$this->dbprefix}messages (timestamp, message, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message, :message_type, :dest_type, :dest_id, :source_type, :source_id)
				";
				$st = $this->db->prepare($sql);
				$st->execute(array(
					':timestamp' => date('Y-m-d H:i:s'), 
					':message_type' => 'room_resync', 
					':message' => 'leave_rooms',
					':dest_type' => 'room', 
					':dest_id' => $room, 
					':source_type' => 'system', 
					':source_id' => 0,
				));
			}
			
			return true;
		}
	}



	/*
	class x7_exception extends exception
	{
	}

	class x7_user
	{
		protected $x7;
		
		protected $by;
		protected $by_id;
		protected $loaded = false;
		protected $db_data;
	
		public function __construct($id = null, $by = null)
		{
			global $x7;
			$this->x7 = $x7;
		
			if($id === null)
			{
				$id = !empty($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
				$by = 'id';
			}
			elseif($by === null)
			{
				$by = 'id';
			}
			elseif(!in_array($by, array('id', 'username', 'email', 'reset_password')))
			{
				throw new x7_exception("Invalid value for `by` parameter");
			}
			elseif(empty($id))
			{
				throw new x7_exception("Invalid parameter value for `id` parameter");
			}
			
			$this->by_id = $id;
			$this->by = $by;
			$this->loaded = false;
		}
		
		public function get_settings()
		{
			$data = $this->data();
			
			$keys = array(
				'enable_sounds',
				'use_default_timestamp_settings',
				'enable_timestamps',
				'ts_24_hour',
				'ts_show_seconds',
				'ts_show_ampm',
				'ts_show_date',
				'enable_styles',
				'message_font_size',
				'message_font_color',
				'message_font_face',
				'location',
				'status_description',
				'status_type',
			);
			
			$settings = array();
			foreach($keys as $key)
			{
				$settings[$key] = $data[$key];
				if(!$settings[$key])
				{
					$settings[$key] = false;
				}
			}
			
			return $settings;
		}
		
		public function data()
		{
			$this->load();
			return $this->db_data;
		}
		
		public function db()
		{
			return $this->x7->db();
		}
		
		public function load()
		{
			if(!$this->loaded)
			{
				$db = $this->db();
				$sql = "
					SELECT
						*
					FROM {$this->x7->dbprefix}users
					WHERE {$this->by} = :value
					LIMIT 1;
				";
				$st = $db->prepare($sql);
				$st->execute(array(
					':value' => $this->by_id
				));
				$this->db_data = $st->fetch();
				$st->closeCursor();
			}
			
			if(!$this->db_data)
			{
				throw new x7_exception("Invalid parameter value for `id` paramter");
			}
		}
		
		public function id()
		{
			if($this->by === 'id')
			{
				return (int)$this->by_id;
			}
			
			$this->load();
			
			return $this->db_data['id'];
		}
		
		public function permissions()
		{
			$id = $this->id();
			
			$sql = "
				SELECT
					`group`.access_admin_panel,
					`group`.create_room
				FROM {$this->x7->dbprefix}groups `group`
				INNER JOIN {$this->x7->dbprefix}users user ON
					user.id = :user_id
					AND user.group_id = group.id
				LIMIT 1;
			";
			$st = $this->db()->prepare($sql);
			$st->execute(array(
				':user_id' => $id
			));
			$row = $st->fetch();
			$st->closeCursor();
			
			if($row)
			{
				return $row;
			}
			
			return array();
		}
		
		public function banned()
		{
			if($this->id() === 1)
			{
				return false;
			}
			
			$this->load();
			
			return (bool)$this->db_data['banned'];
		}
		
		public function leave_rooms()
		{
			$user_id = $this->id();
		
			$rooms = isset($_SESSION['rooms']) ? $_SESSION['rooms'] : array();
			if(empty($rooms))
			{
				return true;
			}
			
			$rooms_str = implode(',', $rooms);
			$sql = "
				DELETE FROM {$this->x7->dbprefix}room_users
				WHERE
					room_id IN ({$rooms_str})
					AND user_id = :user_id
			";
			$st = $this->db()->prepare($sql);
			$st->execute(array(':user_id' => $user_id));
			
			foreach($rooms as $room)
			{
				$sql = "
					INSERT INTO {$this->x7->dbprefix}messages (timestamp, message, message_type, dest_type, dest_id, source_type, source_id) VALUES (:timestamp, :message, :message_type, :dest_type, :dest_id, :source_type, :source_id)
				";
				$st = $this->db()->prepare($sql);
				$st->execute(array(
					':timestamp' => date('Y-m-d H:i:s'), 
					':message_type' => 'room_resync', 
					':message' => 'leave_rooms',
					':dest_type' => 'room', 
					':dest_id' => $room, 
					':source_type' => 'system', 
					':source_id' => 0,
				));
			}
			
			return true;
		}
	}
	
	function x7_check_ip_bans()
	{
		global $x7;
		$db = $x7->db();
	
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$sql = "SELECT * FROM {$x7->dbprefix}bans";
		$st = $db->prepare($sql);
		$st->execute();
		$bans = $st->fetchAll();
		foreach($bans as $ban)
		{
			if(strpos($ban['ip'], '*') !== FALSE)
			{
				$ban['ip'] = preg_quote($ban['ip']);
				$ban['ip'] = str_replace('\*', '(.+?)', $ban['ip']);
				$ban['ip'] = str_replace('#', '\#', $ban['ip']);
				if(preg_match('#' . $ban['ip'] . '#i', $ip))
				{
					return true;
				}
			}
			elseif($ban['ip'] == $ip)
			{
				return true;
			}
		}
		
		return false;
	}

	// @deprecated
	function x7_get_user_id()
	{
		if(!empty($_SESSION['user_id']))
		{
			return (int)$_SESSION['user_id'];
		}
		
		return 0;
	}

	// @deprecated
	function x7_get_user_permissions($id = null)
	{
		global $x7;
		$db = $x7->db();
		
		if(!$id)
		{
			$id = x7_get_user_id();
		}
		
		if(!$id)
		{
			return array();
		}
		
		$sql = "
			SELECT
				`group`.access_admin_panel,
				`group`.create_room
			FROM {$x7->dbprefix}groups `group`
			INNER JOIN {$x7->dbprefix}users user ON
				user.id = :user_id
				AND user.group_id = group.id
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(
			':user_id' => $id
		));
		$row = $st->fetch();
		$st->closeCursor();
		
		if($row)
		{
			return $row;
		}
		
		return array();
	}

	// @deprecated
	function x7_get_user($id = null, $by = 'id')
	{
		global $x7;
		$db = $x7->db();
		
		if(!$id)
		{
			$id = x7_get_user_id();
		}
		
		if(!$id)
		{
			return array();
		}
		
		$sql = "
			SELECT
				id,
				username,
				email,
				group_id,
				banned,
				timestamp,
				ip,
				real_name,
				gender,
				about,
				avatar,
				location,
				status_description,
				status_type
			FROM {$x7->dbprefix}users
			WHERE {$by} = :value
			LIMIT 1;
		";
		$st = $db->prepare($sql);
		$st->execute(array(':value' => $id));
		$row = $st->fetch();
		$st->closeCursor();
		return $row;
	}
	*/