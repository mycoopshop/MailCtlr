<?php

require_once __BASE__.'/model/Storable.php';
class DevTicket extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;
    public $user = 0; //Utente che ha inserito il ticket
        public $category = '';// grafica, bug, funzionalità,
    public $assignedto = 0;
    public $subject = '';
    public $description = '';
    public $priority = '';//Bassa,Media,Alta,Urgente
        public $stato = 'Nuovo';//aperto, in lavorazione, in ricerca,....,chiuso
        public $mantis_id = 0;
}
DevTicket::schemadb_update();
