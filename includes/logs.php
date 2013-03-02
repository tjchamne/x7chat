<?php

	class x7_logs
	{
		protected $x7;
		protected $user;
	
		public function __construct()
		{
			global $x7;
			$this->x7 = $x7;
			
			$x7->load('user');
			$this->user = new x7_user();
		}
		
		public function get_room_logs($room_id, $start = null, $end = null)
		{
			$sql = "
				SELECT
						*
				FROM {$this->x7->dbprefix}messages
				WHERE
					dest_type = 'room'
					AND dest_id = :id
					AND message_type = 'message'
			";
			$params = array(':id' => $room_id);
			
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
	}