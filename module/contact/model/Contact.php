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
    //public $lista = 0;
    public $iscritto = MYSQL_DATETIME;
    public $active = true;
    public $lastedit = MYSQL_DATETIME;
    public $privacy = 0;
    public $privacy_url = "";
    public $type = array('html','text');
    public $hask = "";
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
    public static function makeHask($contact){
        $c = Contact::load($contact);
        $t = time();
        $c->hask = substr(md5($c->email.$t.$c->iscritto),5,11);
        $c->store();
        return $c->hask;
        
    }
    ##
    public static function checkHask($hask){
        return self::query(
                array(
                    'hask' => $hask,
                ));
    }
    ##
    public static function deActive($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 0;
        $c->store();
    }
    ##
    public static function ActivePrivacy($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 1;
        $c->store();
    }
    ##
    public static function Active($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 1;
        $c->store();
    }
    ##
    public static function deActivePrivacy($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 0;
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
    public static function SMTPValidation($email, $probe_address="", $debug=false) {
        $output = "";
        if (!$probe_address) $probe_address = $_SERVER["SERVER_ADMIN"];
        if (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $email, $matches)) {
            $user = $matches[1];
            $domain = $matches[2];
            if (function_exists('checkdnsrr')) {
                if(getmxrr($domain, $mxhosts, $mxweight)) {
                    for($i=0;$i<count($mxhosts);$i++){
                        $mxs[$mxhosts[$i]] = $mxweight[$i];
                    }
                    asort($mxs);
                    $mailers = array_keys($mxs);
                } elseif(checkdnsrr($domain, 'A')) {
                    $mailers[0] = gethostbyname($domain);
                } else {
                    $mailers=array();
                }
                $total = count($mailers);
                if($total > 0) {
                    for($n=0; $n < $total; $n++) {
                        if($debug) { $output .= "Checking server $mailers[$n]...\n";}
                        $connect_timeout = 2;
                        $errno = 0;
                        $errstr = 0;
                        if (preg_match('/^([a-zA-Z0-9\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $probe_address,$fakematches)) {
                            $probe_domain = str_replace("@","",strstr($probe_address, '@'));
                            if($sock = @fsockopen($mailers[$n], 25, $errno , $errstr, $connect_timeout)) {
                                $response = fgets($sock);
                                if($debug) {$output .= "Opening up socket to $mailers[$n]... Success!\n";}
                                stream_set_timeout($sock, 5);
                                $meta = stream_get_meta_data($sock);
                                if($debug) { $output .= "$mailers[$n] replied: $response\n";}
                                $cmds = array(
                                    "HELO $probe_domain",
                                    "MAIL FROM: <$probe_address>",
                                    "RCPT TO: <$email>",
                                    "QUIT",
                                );
                                if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
                                    $codice = trim(substr(trim($response),0,3));
                                    if ($codice=="421") {
                                        $error = $response;
                                        break;
                                    } else {
                                        if($response=="" || $codice=="") {
                                            $codice = "0";
                                        }
                                        $error = "Error: $mailers[$n] said: $response\n";
                                        break;
                                    }
                                    break;
                                }
                                foreach($cmds as $cmd) {
                                    $before = microtime(true);
                                    fputs($sock, "$cmd\r\n");
                                    $response = fgets($sock, 4096);
                                    $t = 1000*(microtime(true)-$before);
                                    if($debug) {$output .= "$cmd\n$response" . "(" . sprintf('%.2f', $t) . " ms)\n";}
                                    if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
                                        $codice = trim(substr(trim($response),0,3));
                                        if ($codice<>"552") {
                                            $error = "Unverified address: $mailers[$n] said: $response";
                                            break 2;
                                        } else {
                                            $error = $response;
                                            break 2;
                                        }
                                    }
                                }
                                fclose($sock);
                                if($debug) { $output .= "Succesful communication with $mailers[$n], no hard errors, assuming OK\n";}
                                break;
                            } elseif($n == $total-1) {
                                $error = "None of the mailservers listed for $domain could be contacted";
                                $codice = "0";
                            }
                        } else {
                            $error = "Il probe_address non è una mail valida.";
                        }
                    }
                } elseif($total <= 0) {
                    $error = "No usable DNS records found for domain '$domain'";
                }
            }
        } else {
            $error = 'Address syntax not correct';
        }
        if($debug) {
            print nl2br(htmlentities($output));
        }
        if(!isset($codice)) {$codice="n.a.";}
        if(isset($error)) return array($error,$codice); else return true;
    }
    
}
Contact::schemadb_update();

