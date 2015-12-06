<?php

require_once __BASE__.'/model/Storable.php';
##
class User extends Storable
{
    ##
    public $id = MYSQL_PRIMARY_KEY;
    public $username = '';
    public $password = '';

    public $nome = '';
    public $cognome = '';
    public $email = '';
    public $telefono = '';
    public $cellulare1 = '';
    public $cellulare2 = '';
    public $photo = '';

    public $role = ['user', 'admin', 'superadmin'];

    public $lastedit = MYSQL_DATETIME;

    ##

    public static function canUserLogin($username, $password)
    {
        return self::ping(['username' => $username, 'password' => md5($password)]);
    }

    ##

    public static function fetchByLogin($username)
    {
        return self::ping(['username' => $username]);
    }

    ##

    public static function getUserIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    ##

    public static function getInfoUser()
    {
        return json_decode(file_get_contents('http://ipinfo.io/'.self::getUserIp()));
    }
}
User::schemadb_update();
