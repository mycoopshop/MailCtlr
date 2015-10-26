<?php
require_once __BASE__.'/model/Storable.php';
##
class User extends Storable {
	##
	public $id = MYSQL_PRIMARY_KEY;
    public $username = "";
	public $password = "";
    //aggiunte info utente SE NON SERVONO SI TOLGONO :D
    public $nome="";
    public $cognome="";
    public $email="";
    public $telefono="";
    public $cellulare1="";
    public $cellulare2="";
    public $photo=""; // :P Immagine di Profilo :D 
    //fine add
	public $role = array('user','admin','superadmin');
    
	##
	public static function canUserLogin($username,$password) {			
		return User::ping(array('username'=>$username,'password'=>md5($password)));		
	}
    
	##
	public static function fetchByLogin($username) {
		return User::ping(array('username'=>$username));		
	}
    
    ##
    
    
    ##
    public static function getUserIp(){
        return $_SERVER['REMOTE_ADDR'];
    }
    
    ##
    public static function getInfoUser(){
        return json_decode(file_get_contents("http://ipinfo.io/".User::getUserIp()));
    }
    
    
    
}
User::schemadb_update();
