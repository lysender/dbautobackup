<?php

ini_set('display_errors', 1);

error_reporting(E_ALL | E_STRICT);

/** 
 * Set time zone
 */
date_default_timezone_set('Asia/Manila');

/** 
 * Initialize the document root
 */
define('DOCROOT', realpath(dirname(__FILE__).'/../').DIRECTORY_SEPARATOR);

/** 
 * Make sure that a config file is setup
 */
if ( ! is_file(DOCROOT.'lib/config.php'))
{
	fwrite(STDERR, 'Config file is not found: lib/config.php - Sample config provided in lib/config.sample.php');
	exit(1);
}