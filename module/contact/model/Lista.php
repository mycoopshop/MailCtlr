<?php
require_once __BASE__.'/model/Storable.php';
##
class Lista extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    
    public $nome = "";
    public $descrizione = "";
    public $creata = MYSQL_DATETIME;
               
    
        	
}

Lista::schemadb_update();
