<?php

	class x7_messages
	{
		protected $x7;
		protected $user;
		
		protected $ts_on;
		protected $ts_fmt;
	
		public function __construct()
		{
			global $x7;
			$this->x7 = $x7;
			
			$x7->load('user');
			$this->user = new x7_user();
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
		
		protected function load_ts_data()
		{
			$settings = $this->user->data();
			if($settings['use_default_timestamp_settings'])
			{
				$settings = $this->default_ts_settings();
			}
			
			$ts_on = ($settings['enable_timestamps'] == 1);
			$ts_fmt = ($settings['ts_24_hour'] ? 'H:i' : 'h:i');
			
			if($settings['ts_show_seconds'])
			{
				$ts_fmt .= ':s';
			}
			
			if(!$settings['ts_24_hour'] && $settings['ts_show_ampm'])
			{
				$ts_fmt .= ' a';
			}
			
			if($settings['ts_show_date'])
			{
				$ts_fmt = 'F d, Y ' . $ts_fmt;
			}
			
			$ts_fmt .= ' T';
			
			$this->ts_on = $ts_on;
			$this->ts_fmt = $ts_fmt;
		}
		
		public function format_timestamp($timestamp)
		{
			if($this->ts_on === null)
			{
				$this->load_ts_data();
			}
			
			if(!$this->ts_on)
			{
				return '';
			}
		
			return date($this->ts_fmt, strtotime($timestamp));
		}
		
		public function apply_filters($message)
		{
			
		}
	}