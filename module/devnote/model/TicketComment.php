<?php
require_once __BASE__.'/model/Storable.php';

class TicketComment extends Storable {	
	public $id = MYSQL_PRIMARY_KEY;	
        
        public $user=0; //Utente che ha inserito il ticket
        public $devticket=0;//Ticket di ref
	public $comment ="";
        public $visible=true;//false per cancellato
}

TicketComment::schemadb_update();