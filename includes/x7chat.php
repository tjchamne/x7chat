<?php

	namespace x7;
	
	use \PDO;
	
	function merge($dest, $with)
	{
		foreach($dest as $field => &$key)
		{
			if(isset($with[$field]))
			{
				$key = $with[$field];
			}
		}
		return $dest;
	}

	function vals()
	{
		$args = func_get_args();
		$source = $args[0];
		$output = array();
		for($index = 1; $index < count($args); $index++)
		{
			$output[] = isset($source[$args[$index]]) ? $source[$args[$index]] : null;
		}
		return $output;
	}

	function val($array, $index)
	{
		return isset($array[$index]) ? $array[$index] : null;
	}

	class x7chat
	{
		const VERSION = '3.2.0a3';
		const VERSION_ID = 30200103;
	
		protected $strings;
		protected $db;
		protected $config;
		protected $system_config;
		public $root;
		public $dbprefix;
		
		protected $users;
		protected $session;
		protected $bans;
		protected $auth;
		protected $integration_api;
		protected $api;
		protected $request;
		protected $mail;
		protected $admin;
		protected $logs;
		protected $messages;
		
		public function api()
		{
			if(!$this->api)
			{
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/Hash.php');
				require_once($this->root . 'includes/libraries/phpseclib/Crypt/AES.php');
				$this->api = new api($this->system_config('api_key'));
			}
			
			return $this->api;
		}
		
		public function messages()
		{
			if(!$this->messages)
			{
				$this->messages = new messages($this);
			}
			
			return $this->messages;
		}
		
		public function logs()
		{
			if(!$this->logs)
			{
				$this->logs = new logs($this);
			}
			
			return $this->logs;
		}
		
		public function admin()
		{
			if(!$this->admin)
			{
				$this->admin = new admin($this);
			}
			
			return $this->admin;
		}
		
		public function mail()
		{
			if(!$this->mail)
			{
				$this->mail = new mail($this);
			}
			
			return $this->mail;
		}
		
		public function request()
		{
			if(!$this->request)
			{
				$this->request = new request($this);
			}
			
			return $this->request;
		}
		
		public function integration_api()
		{
			if(!$this->integration_api)
			{
				$this->integration_api = new integration_api($this);
			}
			
			return $this->integration_api;
		}
		
		public function auth()
		{
			if(!$this->auth)
			{
				$authenticator = !empty($this->system_config['auth_plugin']) ? $this->system_config['auth_plugin'] : 'standalone';
				$authenticator = 'x7\\integration\\' . $authenticator . '\\authenticator';
				$this->auth = new $authenticator($this);
			}
			
			return $this->auth;
		}
		
		public function bans()
		{
			if(!$this->bans)
			{
				$this->bans = new bans($this);
			}
			
			return $this->bans;
		}
		
		public function users()
		{
			if(!$this->users)
			{
				$this->users = new users($this);
			}
			
			return $this->users;
		}
		
		public function session()
		{
			if(!$this->session)
			{
				$this->session = new session($this);
			}
			
			return $this->session;
		}
	
		public function __construct($config)
		{
			date_default_timezone_set('UTC');
		
			$this->system_config = $config;
			$this->root = realpath(dirname(__FILE__) . '/../') . '/';
		
			spl_autoload_register(array($this, 'load_class'));
		}
		
		public function load_class($class)
		{
			$class = explode('\\', $class);
			if(current($class) === 'x7')
			{
				$path = implode('/', array_splice($class, 1));
				$path = $this->root . 'includes/' . $path . '.php';
				if(file_exists($path))
				{
					require_once($path);
					return true;
				}
				
				return false;
			}
		}
		
		public function run()
		{
			$page = isset($_GET['page']) ? $_GET['page'] : 'chat';
			if(preg_match('#[^a-z0-9_]#', $page) || !file_exists($this->root . 'pages/' . $page . '.php')) {
				throw new exception('Invalid page');
			}
			
			$x7 = $this;
			require($this->root . 'pages/' . $page . '.php');
		}
		
		public function fatal_error($error)
		{
			die($error);
		}
		
		public function db()
		{
			if(!$this->db)
			{
				$config = $this->system_config;
				
				$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
				$options = array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
				);
				$db = new PDO($dsn, $config['user'], $config['pass'], $options);
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
				$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				
				$this->db = $db;
				$this->dbprefix = $config['prefix'];
			}
			
			return $this->db;
		}
		
		public function url($page)
		{
			$page = preg_replace("#^([a-z0-9_-]+)\?#i", '$1&', $page);
			$host = $_SERVER['HTTP_HOST'];
			$mode = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://');
			$path = $_SERVER['REQUEST_URI'];
			$url = parse_url($mode . $host . $path);
			$req_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . '?page=' . $page;
			return $req_url;
		}
		
		public function system_config($var)
		{
			if(isset($this->system_config[$var]))
			{
				return $this->system_config[$var];
			}
			
			return null;
		}
		
		public function supports_image_uploads()
		{
			return (is_writable('uploads') && extension_loaded('gd') && ini_get('file_uploads'));
		}
		
		public function upload_max_size_mb()
		{
			return max($this->return_bytes(ini_get('post_max_size')), $this->return_bytes(ini_get('upload_max_filesize')))/1024/1024;
		}
		
		protected function return_bytes($val) {
			$val = trim($val);
			$last = strtolower($val[strlen($val)-1]);
			switch($last) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}

			return $val;
		}
		
		public function config($var)
		{
			if(!$this->config)
			{
				$db = $this->db();
				$sql = "SELECT * FROM {$this->dbprefix}config LIMIT 1";
				$st = $db->prepare($sql);
				$st->execute();
				$row = $st->fetch();
				$this->config = $row;
			}
			
			if(isset($this->config[$var]))
			{
				return $this->config[$var];
			}
			else
			{
				return null;
			}
		}
		
		public function lang($string, $vars = array())
		{
			if(empty($this->strings))
			{
				$this->strings = require($this->root . 'languages/en-us.php');
			}
			
			$string = isset($this->strings[$string]) ? $this->strings[$string] : 'MISSING TRANSLATION: ' . $string;
			
			foreach($vars as $key => $value)
			{
				$string = str_replace($key, $value, $string);
			}
			
			return $string;
		}
		
		public function tl($string)
		{
			if(empty($this->strings))
			{
				require($this->root . 'languages/en-us.php');
			}
			
			echo isset($this->strings[$string]) ? $this->strings[$string] : 'MISSING TRANSLATION: ' . $string;
		}
		
		public function esc($var)
		{
			return htmlentities($var, ENT_QUOTES, 'UTF-8');
		}
		
		public function render($template, $vars = array())
		{
			ob_start();
			$this->display($template, $vars);
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	
		public function display($template, $vars = array())
		{
			$x7 = $this;
			
			if(!isset($vars['errors']))
			{
				$vars['errors'] = $this->session()->get_messages('error');
			}
			
			if(!isset($vars['notices']))
			{
				$vars['notices'] = $this->session()->get_messages('notice');
			}
			
			extract($vars);
			
			$val = function($var) use($vars)
			{
				$inspect = &$vars;
				$parts = explode('.', $var);
				do {
					$part = array_shift($parts);
					if(!isset($inspect[$part]))
					{
						return null;
					}
					$inspect = &$inspect[$part];
				} while($parts);
				
				return $inspect;
			};
			
			$var = function($var) use($vars, $val)
			{
				echo $val($var);
			};
			
			$display = function($template, $extra_vars = array()) use($x7, $vars)
			{
				return $x7->display($template, array_merge($vars, $extra_vars));
			};
			
			$esc = function($value)
			{
				echo htmlentities($value, ENT_QUOTES, 'UTF-8');
			};
			
			$lang = function($string, $vars = array()) use($x7)
			{
				echo $x7->lang($string, $vars);
			};
			
			$url = function($string) use($x7)
			{
				echo $x7->url($string);
			};
			
			require($this->root . 'templates/default/' . $template . '.php');
		}
	}