<?php
require_once __BASE__.'/model/Storable.php';
##
class Email extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    
    public $oggetto = "";
    public $messaggio_html = MYSQL_TEXT;
    public $messaggio_text = MYSQL_TEXT;
    
    public $lista = 0;    
    public $testing = "";
    
    public $created = MYSQL_DATETIME;    
    public $lastedit = MYSQL_DATETIME;
    public $execute = MYSQL_DATETIME;
        	
}
Email::schemadb_update();

