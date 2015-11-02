<?php
require_once __BASE__.'/model/Storable.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Contact.php';

##
class Coda extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    
    
    public $contact_id = 0;
    public $email_id = 0;
    
    public $created = MYSQL_DATETIME;    
    public $execute = MYSQL_DATETIME;    
    public $server_id = 0;
    public $processato = 0;
    public $note = MYSQL_TEXT;
     	
    ##
    public static function addCode($lista="",$email=""){
        $app = App::getInstance();
        
        $iscrizioni = Iscrizioni::getList($lista);        
        foreach ($iscrizioni as $iscrizione) {
            $c = Contact::load($iscrizione->contatto_id);
            if (!$c->active){ 
                $iscrizione->active=0;
            }
            if (!isset($c->id)){
                $iscrizione::delete($iscrizione->id);
            }
            
            Coda::submit(array(
                'contact_id'    =>  $iscrizione->contatto_id,
                'created'       =>  MYSQL_NOW(),
                'email_id'      =>  $email,
                'user_id'       =>  $app->user["id"],
            ));
            
            $iscrizione->store();
            
        }
        
    }
}
Coda::schemadb_update();

