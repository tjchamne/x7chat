<?php

	// X X7 Chat 3 (always 3)
	// M major chat version
	// m minor chat version
	// T release type (1 = alpha, 3 = beta, 5 = release candidate, 7 = final)
	// b build number (resets to 0 each time any other digit increases)
	//                XMMmmTbb
	define('VERSION', 30200103);
	//              3.02.00a03

	function db_connection($config)
	{
		$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
		$db_options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
		);
		$db = new PDO($dsn, $config['user'], $config['pass'], $db_options);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		
		$sql = "SHOW TABLES;";
		$st = $db->prepare($sql);
		$st->execute();
		$tables = $st->fetchAll();
		
		return $db;
	}
	
	function patch_sql($db, $prefix)
	{
		$version = null;
		$applied_patches = array();
		
		try
		{
			$sql = "SELECT `version` FROM {$prefix}config LIMIT 1;";
			$st = $db->prepare($sql);
			$st->execute();
			$version = $st->fetchAll();
			
			if($version)
			{
				$version = $version[0]['version'];
			}
			else
			{
				throw new exception("Invalid config table");
			}
		}
		catch(Exception $ex)
		{
		}
		
		try
		{
			$sql = "SELECT `file` FROM {$prefix}patches WHERE `version` >= :version";
			$st = $db->prepare($sql);
			$st->execute(array(':version' => $version ? $version : 0));
			$applied_patches = $st->fetchAll();
		}
		catch(Exception $ex)
		{
			$sql = "
				CREATE TABLE {$prefix}patches (
					`version` BIGINT UNSIGNED NOT NULL,
					`file` VARCHAR( 255 ) NOT NULL,
					PRIMARY KEY (`version`, `file`)
				) ENGINE = InnoDB;
			";
			$db->query($sql);
		}
	
		$patch_sets = array();
		$patch_dir = dirname(__FILE__) . '/sql/';
		foreach(scandir($patch_dir) as $patch_version)
		{
			if(preg_match('#^3[0-9]{7}$#', $patch_version))
			{
				$patch_sets[$patch_version] = load_patches($patch_dir . $patch_version);
			}
		}
		ksort($patch_sets);
		
		if($version)
		{
			if($version >= 30200102)
			{
				unset($patch_sets[30200102]);
			}
			
			foreach($patch_sets as $patch_version => $patches)
			{
				if($patch_version < VERSION)
				{
					unset($patch_sets[$patch_version]);
				}
				elseif($patch_version == VERSION)
				{
					foreach($applied_patches as $patch)
					{
						if($key = array_search($patch['file'], $patches))
						{
							unset($patch_sets[$patch_version][$key]);
						}
					}
				}
			}
		}
		
		if(!$version)
		{
			$patch_sets = array_reverse($patch_sets, true);
			$patch_sets['new'] = load_patches($patch_dir . 'new');
			$patch_sets = array_reverse($patch_sets, true);
		}
		
		foreach($patch_sets as $patch_version => $patches)
		{
			foreach($patches as $patch)
			{
				$sql = file_get_contents($patch_dir . $patch_version . '/' . $patch);
				$sql = str_ireplace('{$prefix}', $prefix, $sql);
				
				try
				{
					$db->query($sql);
				
					$st = $db->prepare("INSERT INTO {$prefix}patches (`version`, `file`) VALUES (:version, :file)");
					$st->execute(array(
						'version' => ($patch_version === 'new' ? 30200101 : $patch_version),
						'file' => $patch,
					));
				}
				catch(Exception $ex)
				{
					throw new Exception("({$patch_version}/{$patch}) " . $ex->getMessage());
				}
			}
			
			$st = $db->prepare("UPDATE {$prefix}config SET version = :version WHERE version < :version");
			$st->execute(array(
				'version' => ($patch_version === 'new' ? 30200101 : $patch_version),
			));
		}
	}
	
	function load_patches($patch_dir)
	{
		foreach(scandir($patch_dir) as $patch_file)
		{
			if(preg_match('#^([0-9]+)(.+?)\.sql$#', $patch_file, $match))
			{
				$order = $match[1];
				$patch_sets[$order] = $patch_file;
			}
		}
		ksort($patch_sets);
		return $patch_sets;
	}
	
	// @deprecated
	function run_sql($db, $srcdir, $prefix)
	{
		$root = dirname(__FILE__) . '/';
	
		$dir = scandir($root . 'sql/' . $srcdir);
		$patches = array();
		foreach($dir as $file)
		{
			if(preg_match('#^([0-9]+)(.+?)\.sql$#', $file, $match))
			{
				$order = $match[1];
				
				$sql = file_get_contents($root . 'sql/' . $srcdir .'/' . $file);
				$sql = str_replace('{$prefix}', $prefix, $sql);
				
				$patches[$order][$file] = $sql;
			}
		}
		
		ksort($patches);
		
		foreach($patches as $patch_level)
		{
			foreach($patch_level as $file => $patch)
			{
				try
				{
					$st = $db->prepare($patch);
					$st->execute();
				}
				catch(Exception $ex)
				{
					throw new Exception("({$srcdir}/{$file}) " . $ex->getMessage());
				}
			}
		}
	}
	
	function sf($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}