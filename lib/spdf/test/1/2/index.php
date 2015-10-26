<?php

##
ini_set('display_errors',true);
error_reporting(E_ALL);
#xdebug_disable();

##
require_once '../../../spdf.php';

$spdf = new SPDF();

$spdf->addStyle('style.css');

$spdf->text('Hello World!','h1');
$spdf->text('Hello World!','h1');

$spdf->show();