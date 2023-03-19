<?php

require_once 'lib/bootstrap.php';
require DOCROOT.'lib/dbbackup.php';

$backup_manager = new Dbbackup;

$backup_manager->backup()
	->cleanup();