<?php
require_once __BASE__.'/model/Storable.php';
##
class Log extends Storable {
	public $id = MYSQL_PRIMARY_KEY;

    public $action = "";//azione fatta
    public $data = MYSQL_DATETIME;

    //dati del pc client connesso
    public $user_id = 0;
    public $user_ip = "";
    public $user_hostname =  "";
    public $user_city = "";
    public $user_region = "";
    public $user_country = "";
    public $user_loc = "";
    public $user_org = "";
        	
}

Log::schemadb_update();
