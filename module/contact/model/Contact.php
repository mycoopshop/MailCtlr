<?php
require_once __BASE__.'/model/Storable.php';
##
class Contact extends Storable {
	public $id = MYSQL_PRIMARY_KEY;

    public $nome = "";
    public $cognome = "";
    public $telefono = "";
    public $cellulare = "";
    
    public $azienda = "";
    public $indirizzo = "";
    public $piva = "";
    public $cap = "";
    public $citta = "";
    public $provincia = "";
    public $email = "";
    
    
    public $lista = 0;
    
    
    public $iscritto = MYSQL_DATETIME;
    public $active = true;
    public $lastedit = MYSQL_DATETIME;
    
    public $type = array('html','text');
        	
}
Contact::schemadb_update();

