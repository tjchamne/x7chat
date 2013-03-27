<?php

	namespace x7;

	class messages
	{
		protected $x7;
		protected $user_settings_cache;
		
		public function __construct($x7)
		{
			$this->x7 = $x7;
		}
		
		protected function default_ts_settings()
		{
			return array(
				'enable_timestamps' => 1,
				'ts_24_hour' => 0,
				'ts_show_seconds' => 0,
				'ts_show_ampm' => 1,
				'ts_show_date' => 0,
			);
		}
		
		protected function load_ts_data($user)
		{
			if(!isset($this->user_settings_cache[$user->id]))
			{
				if($user->use_default_timestamp_settings)
				{
					$user = clone $user;
					foreach($this->default_ts_settings() as $key => $def_value)
					{
						$user->$key = $def_value;
					}
				}
				
				$ts_on = ($user->enable_timestamps == 1);
				$ts_fmt = '';
				
				if($ts_on)
				{
					$ts_fmt = ($user->ts_24_hour ? 'H:i' : 'h:i');
					
					if($user->ts_show_seconds)
					{
						$ts_fmt .= ':s';
					}
					
					if(!$user->ts_24_hour && $user->ts_show_ampm)
					{
						$ts_fmt .= ' a';
					}
					
					if($user->ts_show_date)
					{
						$ts_fmt = 'F d, Y ' . $ts_fmt;
					}
					
					$ts_fmt .= ' T';
				}
				
				$this->user_settings_cache[$user->id] = $ts_fmt;
			}
			
			return $this->user_settings_cache[$user->id];
		}
		
		public function format_timestamp($user, $timestamp)
		{
			$fmt = $this->load_ts_data($user);
		
			if(!$fmt)
			{
				return '';
			}
		
			return date($fmt, strtotime($timestamp));
		}
		
		public function apply_filters($message)
		{
			
		}
	}