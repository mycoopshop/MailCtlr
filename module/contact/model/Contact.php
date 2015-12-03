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
    public $iscritto = MYSQL_DATETIME;
    public $active = true;
    public $lastedit = MYSQL_DATETIME;
    public $privacy = 0;
    public $privacy_url = "";
    public $type = array('html','text');
    public $token = "";
    public $token_c = 0;
    public $verificato = 0;
    ##
    public static function count($type=""){
        $append = " ";
        switch ($type) {
            case 'verify0':
                $append .= " WHERE verificato = '0' ";
                break;
            case 'verify1':
                $append .= " WHERE verificato = '1' ";
                break;
            default:
                $append .= "";
                break;
        }
        
        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table().$append;
        $res = schemadb::execute('row',$sql);
        return $res['totale'];
    }
    ##
    public static function makeToken($contact){
        $c = Contact::load($contact);
        $t = time();
        $c->token = md5('mailctlr_'.$c->email.$t.$c->iscritto);
        $c->token_c = 1;
        $c->store();
        return $c->token;
    }
    ##
    public static function checkToken($token){
        return self::query(
                array(
                    'token' => $token,
                ));
    }
    ##
    public static function deActive($token){
        $co = self::checkToken($token);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 0;
        $c->verificato = 0;
        $c->store();
    }
    ##
    public static function ActivePrivacy($token){
        $co = self::checkToken($token);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 1;
        $c->active = 1;
        $c->store();
    }
    ##
    public static function Active($token){
        $co = self::checkToken($token);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 1;
        $c->store();
    }
    ##
    public static function deActivePrivacy($token){
        $co = self::checkToken($token);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 0;
        $c->active = 0;
        $c->verificato = 0;
        $c->store();
    }
    ##
    public static function checkContact($c){
        return filter_var($c, FILTER_VALIDATE_EMAIL);
    }
    ##
    public static function duplicate(){
        $sql =      " SELECT c.id, COUNT(c.id) as tot "
                  . " FROM (SELECT tb.* FROM ".self::table()." AS tb ORDER BY id DESC ) as c "
                  . " GROUP BY email "
                  . " HAVING tot > 1 ";
        return schemadb::execute('results',$sql);
    }
    
    ##
    public static function verifyEmail($toemail, $fromemail, $getdetails = false){
        $details = "";
        $email_arr = explode("@", $toemail);
        $domain = array_slice($email_arr, -1);
        $domain = $domain[0];

        // Trim [ and ] from beginning and end of domain string, respectively
        $domain = ltrim($domain, "[");
        $domain = rtrim($domain, "]");

        if( "IPv6:" == substr($domain, 0, strlen("IPv6:")) ) {
            $domain = substr($domain, strlen("IPv6") + 1);
        }

        $mxhosts = array();
        if( filter_var($domain, FILTER_VALIDATE_IP) )
            $mx_ip = $domain;
        else
            getmxrr($domain, $mxhosts, $mxweight);

        if(!empty($mxhosts) )
            $mx_ip = $mxhosts[array_search(min($mxweight), $mxhosts)];
        else {
            if( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
                $record_a = dns_get_record($domain, DNS_A);
            }
            elseif( filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
                $record_a = dns_get_record($domain, DNS_AAAA);
            }

            if( !empty($record_a) )
                $mx_ip = $record_a[0]['ip'];
            else {

                $result   = "invalid";
                $details .= "No suitable MX records found.";

                return ( (true == $getdetails) ? array($result, $details) : $result );
            }
        }

        $connect = @fsockopen($mx_ip, 25); 
        if($connect){ 
            if(preg_match("/^220/i", $out = fgets($connect, 1024))){
                fputs ($connect , "HELO $mx_ip\r\n"); 
                $out = fgets ($connect, 1024);
                $details .= $out."\n";

                fputs ($connect , "MAIL FROM: <$fromemail>\r\n"); 
                $from = fgets ($connect, 1024); 
                $details .= $from."\n";

                fputs ($connect , "RCPT TO: <$toemail>\r\n"); 
                $to = fgets ($connect, 1024);
                $details .= $to."\n";

                fputs ($connect , "QUIT"); 
                fclose($connect);

                if(!preg_match("/^250/i", $from) || !preg_match("/^250/i", $to)){
                    $result = "invalid"; 
                }
                else{
                    $result = "valid";
                }
            } 
        }
        else{
            $result = "invalid";
            $details .= "Could not connect to server";
        }
        if($getdetails){
            return array($result, $details);
        }
        else{
            return $result;
        }
    }

}
Contact::schemadb_update();

