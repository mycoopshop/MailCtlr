<?php
require_once __BASE__.'/model/Storable.php';
class AccountSMTP extends Storable {	
	public $id = MYSQL_PRIMARY_KEY;
    
    public $code = "";
    public $created = MYSQL_DATETIME;
    public $last_edit = MYSQL_DATETIME;
    
    public $name = "";
    
    public $host =  "";
    public $port = "";
    public $connection  = "";
    public $username = "";
    public $password = "";
    public $sender_name = "";
    public $sender_mail = "";
    
    public $replyTo = "";
    
    public $max_mail = 0;
    public $ever = array('day','week','month','year');
    
    
    
    public $send = 0;
    public $last_send = MYSQL_DATETIME;
    public $perc = 0.0;
    
    public $total_send = 0;
    
    public $active = 1;
    
    
    ##
    public static function findServer(){
        $servers = AccountSMTP::query(
                array(
                    'active'  => 1,
                ));
        $use = (OBJECT) array();
        $perc = 110;
        foreach ($servers as $server){
            if ( $server->perc < $perc ){ //trova quello più saturo
                $use = $server;
            }
            if ($server->perc == 0) {
                return $use;
            }
        }
        return $use;
    }
    
}
AccountSMTP::schemadb_update();
