<?php

	namespace x7\integration\wordpress;
	
	use x7\exception,
		x7\model;
	
	class authenticator extends \x7\integration\remote_authenticator
	{
		public function get_url()
		{
			$host = $_SERVER['HTTP_HOST'];
			$port = $_SERVER['SERVER_PORT'];
			$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			$wp_base = substr($url, 0, strpos($url, '/wp-content/plugins/x7chat'));
			return 'http://' . $host . ':' . $port . $wp_base . '/index.php?x7chat=api_message';
		}
	}