<?php
## set debug error
error_reporting(E_ALL);
ini_set('html_errors', True);
ini_set('display_errors', True);
ini_set('display_startup_errors', True);
//xdebug_disable();

## define base dir
define('__BASE__',__DIR__);

## required base library
require_once __BASE__.'/lib/liberty/Liberty.php';

## 
Liberty::debug(true);

## define base constants
if (!defined('__NAME__')) {
	Liberty::trigger_error('[Liberty 102] define constant "__NAME__" in your "index.php"');
}
if (!defined('__MODE__')) {
	Liberty::trigger_error('[Liberty 103] define constant "__MODE__" in your "index.php"');
}

## load config
$config = Liberty::config();

## handle database model
require_once __BASE__.'/lib/schemadb/schemadb.php';

## connect database
$db = schemadb::connect(
	$config['db']['host'],
	$config['db']['user'],
	$config['db']['pass'],
	$config['db']['name'],
	$config['db']['pref']
);

## other constants
define('__URL__',rtrim($config['url'],'/'));
define('__HOME__',rtrim($config['home'],'/'));
define('__PUBLIC__',__URL__.'/public');
