<?php

	namespace x7;
	
	class model
	{
		public function __construct($data = null)
		{
			if(is_array($data))
			{
				$this->load($data);
			}
		}
		
		public function load($data)
		{
			foreach($data as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}