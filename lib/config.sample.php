<?php defined('DOCROOT') or die('No direct script access.');

return array(
	'backup_dir'	=> '/home/user/backup/db/',
	'keep_files'	=> 5,
	'dump_command'	=> '/usr/bin/mysqldump',
	'compressor'	=> '/usr/bin/gzip -9',
	'compressor_ext' => 'gz',
	'db_host' 		=> 'localhost',
	'db_user' 		=> 'root',
	'db_passwd' 	=> 'password',
	'db_names' 		=> array(
		'manila_db',
		'simplestore',
		'tvjuan',
		'discovery_air'
	)
);