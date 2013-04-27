<?php

	namespace x7;
	
	class api
	{
		const MAX_AGE = 14;
	
		protected $key;
	
		public function __construct($key)
		{
			$this->key = $key;
		}
		
		public function get_message($message)
		{
			$key = $this->key;
			
			if($message)
			{
				$message = @base64_decode($message);
				if($message)
				{
					$message = @unserialize($message);
					if($message)
					{
						$cipher = new \Crypt_AES(CRYPT_AES_MODE_ECB);
						$cipher->setPassword($key, 'pbkdf2', 'sha256', $message['s'], 1000);
						
						if(@gmmktime() - $message['t'] > self::MAX_AGE)
						{
							return false;
						}
						
						$message = $cipher->decrypt($message['p']);
						if($message)
						{
							$message = @unserialize($message);
							if($message)
							{
								return $message;
							}
						}
					}
				}
			}
			
			return false;
		}
		
		public function create_message(model\api_message $message)
		{
			$payload = serialize($message);
			$key = $this->key;
			$salt = crypt(microtime() . mt_rand(0, mt_getrandmax()));
			
			$cipher = new \Crypt_AES(CRYPT_AES_MODE_ECB);
			$cipher->setPassword($key, 'pbkdf2', 'sha256', $salt, 1000);
			$payload_enc = $cipher->encrypt($payload);
			$message = base64_encode(serialize(array(
				's' => $salt, 
				'p' => $payload_enc,
				't' => @gmmktime(),
			)));
			
			return $message;
		}
	}