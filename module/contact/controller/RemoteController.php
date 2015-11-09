<?php

require_once __BASE__.'/module/config/model/AccountSMTP.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/sender/model/Coda.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/lib/smtpvalidate.php';

class RemoteController {
    
    ##
	public function activePrivacyAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::ActivePrivacy($hask);
        echo "{$hask} ok!";
	}
    
    ##
	public function deactivePrivacyAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::deActivePrivacy($hask);
        echo "{$hask} ok!";
	}
    
    ##
	public function deActiveAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::deActive($hask);
        echo "{$hask} ok!";
	}
    
    ##
	public function ActiveAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::Active($hask);
        echo "{$hask} ok!";
	}
    
    ##
    public function deleteDuplicateAction(){
        $reply = "";
        $reply .= "DELETE DUPLICATE START<br />";
        $d = Contact::duplicate();
        foreach ($d as $c){
            //var_dump($c);echo $c['id'];die();
            //$cs = Contact::load($c['id']);
            $reply .= "Eliminato contatto  {$c['id']} duplicato {$c['tot']} volte";
            Contact::delete($c['id']);
            $i = Iscrizioni::query(array('contatto_id' => $c['id']));
            $s = Coda::query(array('contact_id'=>$c['id']));
            foreach ($i as $l) Iscrizioni::delete($l->id);
            foreach ($s as $i) Coda::delete($i->id);
            $reply .= " -- Pulite anche Coda e Iscrizione<br />";
        }
        $reply .= "DELETE DUPLICATE STOP<br />";
        
        $json = array(
            'message' => $reply,
        );
        
        echo json_encode($json);
        
    }
    
    ##
    public function cleanContactAction(){
        $tot = $_POST['tot'];
        $current =  $tot - $_POST['curr'];
        $prog = $current * 100 / $tot;
        $reply = "";
        $v = new Minibots();
        $d = Contact::query(
                array(
                    'verificato'  => 0,
                    'limit'       => 1,
                ));
        foreach ($d as $c ){
            if (!filter_var($c->email, FILTER_VALIDATE_EMAIL)){
                $reply .= "Contatto {$c->id} non valido! [{$c->email}]...";
                Contact::delete($c->id);
                $reply .= "...ELIMINATO " . "\n\r";
            }else{
                $rx = $v->doSMTPValidation($c->email,"vincenzo@ctlr.it");
                if ($rx[1]==550){
                    $reply .= "Contatto {$c->id} non valido [VERIFICA SMTP FALLITA]! [{$c->email}]...";
                    Contact::delete($c->id);
                    $reply .= "...ELIMINATO " . "\n\r";
                }else{
                    $c->verificato = 1;
                    $c->store();
                }
            }
        }
        $json = array(
            'progress' => $prog,
            'remain' => $tot-$current-1,
            'message' => $reply,
        );
        echo json_encode($json);
    }
    
    ##
    public static function activeServerAction(){ //azzera giornalmente i conteggi
        $servers = AccountSMTP::all(); 
        $reply = '';
        foreach ($servers as $server){
            $diff = abs(strtotime(MYSQL_NOW()) - strtotime($server->last_send));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $day = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            
            switch ($server->ever) {
                case 'day':
                    if ($day >= DAY ){
                        $server->max_mail_day = $server->max_mail / DAY;
                        $server->active = 1;
                        $server->send = 0;
                        $reply .= $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'month':
                    if ($day >= MONTH ){
                        $server->max_mail_day = $server->max_mail / MONTH;
                        $server->active = 1;
                        $server->send = 0;
                        $reply .= $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'year':
                    if ($day >= YEAR ){
                        $server->max_mail_day = $server->max_mail / YEAR;
                        $server->active = 1;
                        $server->send = 0;
                        $reply .= $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'week':
                    if ($day >= WEEK ){
                        $server->max_mail_day = $server->max_mail / WEEK;
                        $server->active = 1;
                        $server->send = 0;
                        $reply .= $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'onetime':
                    if ($server->active){
                        $server->max_mail_day = $server->max_mail;
                        $reply .= $server->id." ONETIME SERVER TOTAL SEND {$server->send}.<br />";
                    }
                    break;
            }
            $server->store();
        } 
        $reply .= "ATTIVATI TUTTI I SERVER.";
        
        echo $reply;
    }
    
}