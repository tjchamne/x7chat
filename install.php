<?php

	function db_connection($config)
	{
		$dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8';
		$db_options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => FALSE,
		);
		$db = new PDO($dsn, $config['user'], $config['pass'], $options);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		
		$sql = "SHOW TABLES;";
		$st = $db->prepare($sql);
		$st->execute();
		$tables = $st->fetchAll();
		
		return $db;
	}
	
	function check_for_install($config, $db)
	{
		try
		{
			$sql = "SELECT `version` FROM {$config['prefix']}config LIMIT 1;";
			$st = $db->prepare($sql);
			$st->execute();
			$config = $st->fetchAll();
			
			if(!$config)
			{
				die('Chatroom configuration table exists, but configuration data does not, setup will exit now.');
			}
			
			$config = current($config);
			if((int)$config['version'] !== 30200101)
			{
				die('The chatroom database is running an unrecognized version of X7 Chat, setup will exit now.');
			}
			
			// Expect to hit this die statement after X7 Chat is installed
			die('X7 Chat is already installed, setup will exit now.');
		}
		catch(Exception $ex)
		{
			// Expected
		}
	}
	
	if(!file_exists('./config.php'))
	{
		die('The config.php file is missing, setup will exit now.');
	}
	
	$config_writable = is_writable('./config.php');
	
	$config_file_ok = false;
	
	ob_start();
	$config_file = require('./config.php');
	ob_end_clean();
	
	if(is_array($config_file) && !empty($config_file['dbname']))
	{
		try
		{
			$db = db_connection($config_file);
		}
		catch(Exception $ex)
		{
			die('The config.php file contains invalid database details, setup will exit now.');
		}
		
		check_for_install($config_file, $db);
		
		$config_file_ok = true;
	}
	else
	{
		$config_file = null;
	}

	if(!empty($_POST))
	{
		if(empty($_POST['admin_username']))
		{
			die("Please enter an admin username.");
		}
		
		if(empty($_POST['admin_password']))
		{
			die("Please enter an admin password.");
		}
		
		if($_POST['admin_password'] != $_POST['retype_admin_password'])
		{
			die("The admin passwords you entered do not match.");
		}
		
		if(!filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL))
		{
			die("The E-Mail address you entered is not valid.");
		}
		
		if(empty($_POST['title']))
		{
			$_POST['title'] = 'Chatroom';
		}
		
		try
		{
			$db = db_connection($_POST);
		}
		catch(Exception $ex)
		{
			die('Database connection failed: ' . $ex->getMessage());
		}
		
		if(!$config_file_ok)
		{
			$config_data = array(
				'user' => $_POST['user'],
				'pass' => $_POST['pass'],
				'dbname' => $_POST['dbname'],
				'host' => $_POST['host'],
				'prefix' => $_POST['prefix'],
			);
			
			check_for_install($config_data, $db);
			
			if(!$config_writable)
			{
				die("Please enter the following data into your config.php file, then click Continue: <hr /><pre>&lt;?php return " . var_export($config_data, 1) . ";</pre><hr />");
			}
			else
			{
				file_put_contents("config.php", "<?php return " . var_export($config_data, 1) . ";");
			}
		}
		
		$prefix = isset($config_data['prefix']) ? $config_data['prefix'] : $config_file['prefix'];
		
		$dir = scandir('./install/sql/new');
		$patches = array();
		foreach($dir as $file)
		{
			if(preg_match('#^([0-9]+)(.+?)\.sql$#', $file, $match))
			{
				$order = $match[1];
				
				$sql = file_get_contents('./install/sql/new/' . $file);
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
					die("Fatal database error: {$ex->getMessage()} in file {$file}<br /><br />Setup will exit now.");
				}
			}
		}
		
		require('./includes/libraries/phpass/PasswordHash.php');
		$phpass = new PasswordHash(8, false);
		
		try
		{
			$sql = "INSERT INTO {$prefix}users (`id`, `username`, `password`, `email`, `group_id`) VALUES (1, :username, :password, :email, 1);";
			$st = $db->prepare($sql);
			$st->execute(array(
				':username' => $_POST['admin_username'],
				':password' => $phpass->HashPassword($_POST['admin_password']),
				':email' => $_POST['admin_email'],
			));
		}
		catch(Exception $ex)
		{
			die("Failed to create admin account: " . $ex->getMessage());
		}
		
		try
		{
			$sql = "
				UPDATE {$prefix}config SET
					title = :title,
					from_address = :email
				LIMIT 1;
			";
			$st = $db->prepare($sql);
			$st->execute(array(
				':title' => $_POST['title'],
				':email' => $_POST['admin_email'],
			));
		}
		catch(Exception $ex)
		{
			die("Failed to update default settings: " . $ex->getMessage());
		}
		
		
		
		die("Installation complete");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Install X7 Chat 3.2</title>
		<script type="text/javascript" src="scripts/jquery.js"></script>
		<style type="text/css">
			table
			{
				width: 100%;
				font-size: 14px;
				color: #555555;
			}
			
			thead th
			{
				font-size: 16px;
				padding: 2px;
				color: black;
			}
			
			tbody th
			{
				text-align: left;
				border-top: 1px solid #DDDDDD;
				padding: 2px;
			}
			
			tbody td
			{
				text-align: center;
				border-top: 1px solid #DDDDDD;
				padding: 2px;
			}
			
			.ok
			{
				color: #00AB2E;
			}
			
			.warn
			{
				color: #DBCD00;
			}
			
			.fail
			{
				color: #C20000;
				font-weight: bold;
			}
			
			hr
			{
				border: 0;
				color: #DDDDDD;
				background-color: #DDDDDD;
				height: 1px;
			}
			
			label
			{
				width: 200px;
				display: inline;
			}
			
			#page_wrapper
			{
				position: relative;
				width: 700px;
				margin: auto;
				font-family: Calibri;
				font-size: 14px;
				background: #FAFAFA;
				color: #555555;
				border: 1px solid #AAAAAA;
				box-shadow: 0px 0px 2px #555555;
			}
			
			#page_header
			{
				background: #F0F0F0;
				border-top: 1px solid #FAFAFA;
				border-bottom: 1px solid #FFFFFF;
			}
			
			#header_inner
			{
				border-bottom: 1px solid #AAAAAA;
			}
			
			#header_menu
			{
				float: right;
			}
			
			#page_logo
			{
				margin: 10px;
				font-size: 18px;
				font-weight: bold;
				float: left;
			}
			
			a,
			a:visited
			{
				color: #0066ff;
				text-decoration: none;
			}

			a:hover
			{
				color: #000000;
			}
			
			#page_content
			{
				margin-bottom: 30px;
			}

			#inner_page_content
			{
				position: relative;
				padding: 0px;
			}

			.inner_page
			{
				padding: 5px;
			}

			#page_footer
			{
				position: absolute;
				bottom: 0px;
				width: 100%;
				text-align: center;
				font-size: 10px;
				font-family: Arial, Sans-serif;
				background: #F0F0F0;
				border-top: 1px solid #FFFFFF;
				height: 26px;
			}

			#footer_inner
			{
				border-top: 1px solid #AAAAAA;
				padding: 5px;
				border-bottom: 1px solid #FAFAFA;
				height: 14px;
				line-height: 14px;
			}
		</style>
		<script type="text/javascript">
			$(function() {
				$("#dbform").bind('submit', function(ev) {
					ev.preventDefault();
					$('#continue').attr('disabled', 'disabled');
					$('#continue').attr('value', 'Please Wait');
					
					$.post('install.php', $(this).serialize(), function(data) {
						$('#continue').attr('disabled', false);
						$('#continue').attr('value', 'Continue');
						$("#upper_content").html(data);
					});
				});
			});
		</script>
	</head>
	<body>
		<div id="page_wrapper">
			<div id="page_header">
				<div id="header_inner">
					<div id="page_logo">Install X7 Chat 3.2</div>
					<div id="header_menu">
						
					</div>
					<div style="clear: both;"></div>
				</div>
			</div>
			<div id="page_content">
				<div id="page_content_inner">
					<div class="inner_page">
						<div id="upper_content">
							<table cellspacing="0" cellpadding="0">
								<thead>
									<tr>
										<th>Check</th>
										<th>Server Value</th>
										<th>Required Value</th>
										<th>Result</th>
										<th>Required Action</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th>PHP Version</th>
										<td><?php echo phpversion(); ?></td>
										<td>5.3.0</td>
										<?php if(version_compare(phpversion(), '5.3.0', '>=')): ?>
											<td>
												<span class='ok'>OK</span>
												</td>
											<td>&nbsp;</td>
										<?php else: ?>
											<td>
												<span class='fail'>FAIL</span>
												<?php $fail = true; ?>
											</td>
											<td>Upgrade PHP</td>
										<?php endif; ?>
									</tr>
									<tr>
										<th>Magic Quotes GPC</th>
										<td><?php var_export((bool)get_magic_quotes_gpc()); ?></td>
										<td>false</td>
										<?php if(get_magic_quotes_gpc()): ?>
											<td>
												<span class='warn'>WARN</span>
												</td>
											<td>Set magic_quotes_gpc to off</td>
										<?php else: ?>
											<td>
												<span class='ok'>OK</span>
											</td>
											<td>&nbsp;</td>
										<?php endif; ?>
									</tr>
									<tr>
										<th>Magic Quotes Runtime</th>
										<td><?php var_export((bool)get_magic_quotes_runtime()); ?></td>
										<td>false</td>
										<?php if(get_magic_quotes_runtime()): ?>
											<td>
												<span class='fail'>FAIL</span>
												</td>
											<td>Set magic_quotes_runtime to off</td>
											<?php $fail = true; ?>
										<?php else: ?>
											<td>
												<span class='ok'>OK</span>
											</td>
											<td>&nbsp;</td>
										<?php endif; ?>
									</tr>
									<tr>
										<th>Magic Quotes Sybase</th>
										<td><?php var_export((bool)ini_get('magic_quotes_sybase')); ?></td>
										<td>false</td>
										<?php if(ini_get('magic_quotes_sybase')): ?>
											<td>
												<span class='fail'>FAIL</span>
												</td>
											<td>Set magic_quotes_sybase to off</td>
											<?php $fail = true; ?>
										<?php else: ?>
											<td>
												<span class='ok'>OK</span>
											</td>
											<td>&nbsp;</td>
										<?php endif; ?>
									</tr>
									<tr>
										<th>File Uploads</th>
										<td><?php var_export((bool)ini_get('file_uploads')); ?></td>
										<td>true</td>
										<?php if(!ini_get('file_uploads')): ?>
											<td>
												<span class='warn'>WARN</span>
												</td>
											<td>Set file_uploads to on</td>
										<?php else: ?>
											<td>
												<span class='ok'>OK</span>
											</td>
											<td>&nbsp;</td>
										<?php endif; ?>
									</tr>
									<tr>
										<th>config.php is writable</th>
										<td><?php var_export($config_writable); ?></td>
										<td>true</td>
										<?php if(!$config_writable): ?>
											<td>
												<span class='warn'>WARN</span>
												</td>
											<td>Make config.php writable or create it manually</td>
										<?php else: ?>
											<td>
												<span class='ok'>OK</span>
											</td>
											<td>&nbsp;</td>
										<?php endif; ?>
									</tr>
								</tbody>
							</table>
						</div>
						
						<?php if(empty($fail)): ?>
							<form id="dbform">
								<?php if(!$config): ?>
									<h2>Database Connection Details</h2>
									<b><label for="host">Database Host</label></b>
									<input type="text" name="host" value="localhost" />
									<hr />
									<b><label for="user">Database Username</label></b>
									<input type="text" name="user" value="" />
									<hr />
									<b><label for="pass">Database Password</label></b>
									<input type="password" name="pass" value="" />
									<hr />
									<b><label for="dbname">Database Name</label></b>
									<input type="text" name="dbname" value="" />
									<hr />
									<b><label for="prefix">Table Prefix</label></b>
									<input type="text" name="prefix" value="x7chat_" />
									<hr />
								<?php endif; ?>
								<h2>Admin Account Details</h2>
								<b><label for="admin_username">Admin Username</label></b>
								<input type="text" name="admin_username" value="" />
								<hr />
								<b><label for="admin_username">Admin Password</label></b>
								<input type="password" name="admin_password" value="" />
								<hr />
								<b><label for="retype_admin_password">Retype Admin Password</label></b>
								<input type="password" name="retype_admin_password" value="" />
								<hr />
								<b><label for="admin_email">Admin E-Mail</label></b>
								<input type="text" name="admin_email" value="" />
								<hr />
								<h2>Chatroom Details</h2>
								<b><label for="title">Chatroom Name</label></b>
								<input type="text" name="title" value="" />
								<hr />
								<input id="continue" type="submit" value="Continue" />
							</form>
						<?php else: ?>
							<p>One or more critical checks failed.  Please correct them before installing X7 Chat.</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div id="page_footer">
				<div id="footer_inner">
					<a href="http://www.x7chat.com/" target="_blank">Powered By X7 Chat</a>
				</div>
			</div>
		</div>
	</body>
</html>