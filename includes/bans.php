<?php

	namespace x7;
	
	class bans
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
		
		public function check_ip_ban($ip)
		{
			$sql = "SELECT * FROM {$this->dbprefix}bans";
			$st = $this->db->prepare($sql);
			$st->execute();
			$bans = $st->fetchAll();
			
			try
			{
				foreach($bans as $ban)
				{
					if(strpos($ban['ip'], '*') !== FALSE)
					{
						$ban['ip'] = preg_quote($ban['ip']);
						$ban['ip'] = str_replace('\*', '(.+?)', $ban['ip']);
						$ban['ip'] = str_replace('#', '\#', $ban['ip']);
						if(preg_match('#' . $ban['ip'] . '#i', $ip))
						{
							throw new exception\user_banned;
						}
					}
					elseif($ban['ip'] == $ip)
					{
						throw new exception\user_banned;
					}
				}
			}
			catch(exception\user_banned $ex)
			{
				throw $ex;
			}
		}
		
		public function check_user_ban(model\user $user)
		{
			if($user->banned)
			{
				throw new exception\user_banned;
			}
		}
	}