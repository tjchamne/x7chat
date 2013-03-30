<?php

	namespace x7;
	
	class admin
	{
		protected $x7;
		
		public function __construct($x7)
		{
			$this->x7 = $x7;
		}
		
		public function generate_admin_menu($current_page)
		{
			$menu = array(
				'news' => array(
				),
				'settings' => array(
				),
				'word_filter' => array(
					'href' => 'admin_list_word_filters',
					'items' => array(
						'list_word_filters' => array(
						),
						'create_word_filter' => array(
							'href' => 'admin_edit_word_filter',
						),
						'edit_word_filter' => array(
							'hidden' => true,
						),
					),
				),
				'smilies' => array(
					'href' => 'admin_list_smilies',
					'items' => array(
						'list_smilies' => array(
						),
						'create_smiley' => array(
							'href' => 'admin_edit_smiley',
						),
						'edit_smiley' => array(
							'hidden' => true,
						),
					),
				),
				'users' => array(
					'href' => 'admin_list_users',
					'items' => array(
						'list_users' => array(
						),
						'create_user' => array(
							'href' => 'admin_edit_user',
						),
						'edit_user' => array(
							'hidden' => true,
						),
						'delete_user' => array(
							'hidden' => true,
						),
					),
				),
				'groups' => array(
					'href' => 'admin_list_groups',
					'items' => array(
						'list_groups' => array(
						),
						'create_group' => array(
							'href' => 'admin_edit_group',
						),
						'edit_group' => array(
							'hidden' => true,
						),
						'delete_group' => array(
							'hidden' => true,
						),
					),
				),
				'rooms' => array(
					'href' => 'admin_list_rooms',
					'items' => array(
						'list_rooms' => array(
						),
						'create_room' => array(
							'href' => 'admin_edit_room',
						),
						'edit_room' => array(
							'hidden' => true,
						),
					),
				),
			);
			
			$this->process_admin_menu($menu, $current_page);
			
			return $menu;
		}
		
		protected function process_admin_menu(&$menu, $current_page)
		{
			$return = false;
			
			foreach($menu as $key => &$item)
			{
				if(empty($item['href']))
				{
					$item['href'] = 'admin_' . $key;
				}
				
				if(empty($item['label']))
				{
					$item['label'] = $this->x7->lang('admin_' . $key . '_button');
				}
				
				if(empty($item['hidden']))
				{
					$item['hidden'] = false;
				}
				
				$item['active'] = false;
				
				if(isset($item['items']))
				{
					if($this->process_admin_menu($item['items'], $current_page))
					{
						$item['active'] = true;
						$return = true;
					}
				}
				else
				{
					$item['items'] = array();
				}
				
				if($key == $current_page)
				{
					$item['active'] = true;
					$return = true;
				}
			}
			
			return $return;
		}
	}
	