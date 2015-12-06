<?php

## define base dir
define('__BASE__',__DIR__);

## required base library
require_once __BASE__.'/lib/liberty/Liberty.php';

## handle database model
require_once __BASE__.'/lib/schemadb/schemadb.php';

## define base constants
if (!defined('__NAME__')) {
	Liberty::trigger_error('[Liberty 102] define constant "__NAME__" in your "index.php"');
}
if (!defined('__MODE__')) {
	Liberty::trigger_error('[Liberty 103] define constant "__MODE__" in your "index.php"');
}

## load config
$config = Liberty::config();
$db = "";

if ( $config['install'] == 0 ) {
    $config['default']['theme'] = 'default';
    $config['default']['controller'] = 'Install';
    $config['default']['action'] = 'index';
    $config['modules'][] = 'install';
    $config['url'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $config['home'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $config['version'] = "0.1";
    $config['debug'] = "false";
}else{
    ## connect database
    $db = schemadb::connect(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name'],
        $config['db']['pref']
    );

    require_once __BASE__.'/module/config/model/Options.php';
    $c = Options::getOptions($config['type']);
    $config = array_merge($config,$c);
    $config['modules'][] = 'install';
    
}

## set debug error
Liberty::debug($config['debug']);

$lang = isset($config['lang']) && $config['lang'] != "" ? $config['lang'] : "en";
$email = isset($config['mail']) && $config['mail'] != "" ? $config['mail'] : "local@local.host";

## other constants
define('__URL__',rtrim($config['url'],'/'));
define('__HOME__',rtrim($config['home'],'/'));
define('__PUBLIC__',__URL__.'/public');
define('__VERSION__',rtrim( $config['version']) );
define('__LANG__',$lang);
define('__EMAIL__',$email);  
