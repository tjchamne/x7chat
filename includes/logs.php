<?php

	namespace x7;

	class logs
	{
		protected $x7;
		protected $db;
		protected $dbprefix;
	
		public function __construct($x7)
		{
			$this->x7 = $x7;
			$this->db = $x7->db();
			$this->dbprefix = $x7->dbprefix;
		}
		
		public function get_visible_rooms($user)
		{
			$sql = "
				SELECT DISTINCT
					online.room_id,
					room.name
				FROM {$this->dbprefix}online AS online
				LEFT JOIN {$this->dbprefix}rooms AS room ON
					room.id = online.room_id
				WHERE
					online.user_id = :user_id
			";
			$st = $this->db->prepare($sql);
			$st->execute(array(':user_id' => $user->id));
			return $st->fetchAll();
		}
		
		public function get_room_logs($user, $room_id, $start = null, $end = null)
		{
			$users = $this->x7->users();
		
			$restrict = '';
			$params = array(
				':id' => $room_id,
			);
			
			if(!$users->has_permission($user, 'view_unrestricted_logs'))
			{
				$restrict = "
					INNER JOIN {$this->dbprefix}online AS online ON
						online.user_id = :user_id
						AND online.room_id = :id
						AND online.join_timestamp <= message.timestamp
						AND (online.part_timestamp >= message.timestamp OR online.part_timestamp IS NULL)
				";
				$params[':user_id'] = $user->id;
			}
		
			$sql = "
				SELECT
						message.*
				FROM {$this->dbprefix}messages AS message
				{$restrict}
				WHERE
					dest_type = 'room'
					AND dest_id = :id
					AND message_type = 'message'
				ORDER BY
					timestamp ASC
			";
			
			if($start)
			{
				$sql .= " AND timestamp >= :start";
				$params[':start'] = $start->format('Y-m-d H:i:s');
			}
			
			if($end)
			{
				$sql .= " AND timestamp <= :end";
				$params[':end'] = $start->format('Y-m-d H:i:s');
			}
			
			$st = $this->db->prepare($sql);
			$st->execute($params);
			return $st->fetchAll();
		}
		
		/*
		public function get_user_logs($user_id, $other_user_id = null, $start = null, $end = null)
		{
			$sql = "
				SELECT
						*
				FROM {$this->x7->dbprefix}messages
				WHERE
					dest_type = 'user'
					AND source_type = 'user'
					AND message_type = 'message'
			";
			$params = array(
				':user_id' => $user_id
			);
			
			if($other_user_id)
			{
				$sql .= "
					AND
					(
						(
							dest_id = :user_id
							AND source_id = :other_user_id
						)
						OR
						(
							dest_id = :other_user_id
							AND source_id = :user_id
						)
					)
				";
				$params[':other_user_id'] = $other_user_id;
			}
			else
			{
				$sql .= "
					AND
					(
						dest_id = :user_id
						OR source_id = :user_id
					)
				";
			}
			
			if($start)
			{
				$sql .= " AND timestamp >= :start";
				$params[':start'] = $start->format('Y-m-d H:i:s');
			}
			
			if($end)
			{
				$sql .= " AND timestamp <= :end";
				$params[':end'] = $start->format('Y-m-d H:i:s');
			}
			
			$st = $this->x7->db()->prepare($sql);
			$st->execute($params);
			return $st->fetchAll();
		}
		*/
	}