<?php

require_once __BASE__.'/module/config/model/AccountSMTP.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/sender/model/Coda.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';
require_once __BASE__.'/module/sender/model/Email.php';

class RemoteController {
    
    ##
	public function activePrivacyAction() {		
		$app = App::getInstance();		
		$token = (string) $app->getUrlParam('token');
        Contact::ActivePrivacy($token);
        echo "{$token} ok!";
	}
    ##
	public function deactivePrivacyAction() {		
		$app = App::getInstance();		
		$token = (string) $app->getUrlParam('token');
        Contact::deActivePrivacy($token);
        echo "NON HAI ACCONSENTITO AL TRATTEMENTO DELLA PRIVACY, SARI CANCELLATO DAL NOSTRO DATABASE A BREVE! IL TUO ID: {$token}";
	}
    ##
	public function deActiveAction() {		
		$app = App::getInstance();		
		$token = (string) $app->getUrlParam('token');
        Contact::deActive($token);
        echo "SEI STATO CANCELLATO DAL NOSTRO DATABASE! IL TUO ID: {$token}";
	}
    ##
	public function ActiveAction() {		
		$app = App::getInstance();		
		$token = (string) $app->getUrlParam('token');
        Contact::Active($token);
        echo "SEI STATO INSERITO DAL NOSTRO DATABASE! IL TUO ID:{$token}";
	}
    ##
    public function deleteDuplicateAction(){
        $reply = "DELETE DUPLICATE START". "\r\n";
        $d = Contact::duplicate();
        foreach ($d as $c){
            $reply .= "Eliminato contatto  {$c['id']} duplicato {$c['tot']} volte";
            Contact::delete($c['id']);
            $is = Iscrizioni::query(array('contatto_id' => $c['id']));
            $cs = Coda::query(array('contact_id'=>$c['id']));
            foreach ($is as $i) Iscrizioni::delete($i->id);
            foreach ($cs as $i) Coda::delete($i->id);
            $reply .= " -- Pulite anche Coda e Iscrizione". "\r\n";
        }
        $reply .= "DELETE DUPLICATE STOP". "\r\n";
        
        $json = array(
            'message' => $reply,
        );
        return json_encode($json);
    }
    
    ##
    public function createTokenAction(){
        $cs = Contact::query(array('token_c'=>0,'limit'=>10));
        $reply = "";
        foreach ($cs as $c ){
            $token = Contact::makeToken($c->id);
            $reply .= "C: {$c->id}"."\t"."TOKEN: {$token} OK<br />";
        }
        echo $reply;
    }
    
    ##
    public function cleanContactAction(){
        $app = App::getInstance();
        $tot = isset($_POST['tot']) && $_POST['tot'] > 1 ? $_POST['tot'] : 1;
        $curr = isset($_POST['curr']) && $_POST['curr'] > 1 ? $_POST['curr'] : 1;
        $current =  $tot - $curr;
        
        $prog = $current * 100 / $tot;
        $reply = "";
        $dup = isset($_POST['dup']) && $_POST['dup'] == 1 ? 1:0;
        $lis = isset($_POST['lista']) && $_POST['lista'] > 0 ? 1 : 0;
        $limit = isset($_POST['limit']) && $_POST['limit'] > 1 ? $_POST['limit'] : 1;
        $privacy = isset($_POST['privacy']) && $_POST['privacy'] > 0 ? 1 : 0;
        $active = isset($_POST['active']) && $_POST['active'] > 0 ? 1 : 0;
        $p_email = isset($_POST['p_email']) && $_POST['p_email'] > 0 ? $_POST['p_email'] : 0;
        
        if ($dup == 1){
            $d = json_decode($this->deleteDuplicateAction());
            $reply .= $d->message;            
        }
        
        $d = Contact::query(
                array(
                    'verificato'  => 0,
                    'limit'       => $limit,
                ));
        foreach ($d as $c ){
            if (!filter_var($c->email, FILTER_VALIDATE_EMAIL)){
                $reply .= "Contatto {$c->id} non valido [FORMATO NON VALIDO]! [{$c->email}]...";
                Contact::delete($c->id);
                $reply .= "...ELIMINATO " . "\r\n";
            }else{
                $rx = Contact::verifyEmail($c->email,__EMAIL__);
                //echo $rx;die();
                if ($rx=='invalid'){
                    $reply .= "Contatto {$c->id} non valido [VERIFICA SMTP FALLITA]! [{$c->email}]...";
                    Contact::delete($c->id);
                    
                    $reply .= "...ELIMINATO " . "\r\n";
                }else{
                    if ($active==1 && !$c->active){
                        $reply .= "Contatto {$c->id} non attivo! [{$c->email}]...";
                        Contact::delete($c->id);
                        $reply.= "...ELIMINATO "."\r\n";
                    }
                    if ($privacy==1 && $p_email>0 && !$c->privacy){
                        Coda::submit(array(
                            'contact_id' => $c->id,
                            'email_id' => $p_email,
                            'created' => MYSQL_NOW(),
                            'user_id' => $app->user['id'],
                        ));
                    }
                    if ($lis==1){
                        Iscrizioni::submit(array(
                            'lista_id'          => mysql_real_escape_string($_POST['lista']),
                            'contatto_id'       => $c->id,
                            'creata'            => MYSQL_NOW(),
                            'user_id'           => $app->user['id'],
                        ));
                    }
                    $c->email = strtolower($c->email);
                    $c->verificato = 1;
                    $c->store();
                }
            }
        }        
        $json = array(
            'progress' => $prog,
            'remain' => $tot-$current-$limit,
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
    
    ##
    public function iscrizioniDuplicateAction(){
        $d = Iscrizioni::duplicate();
        //echo "<pre>";var_dump($d);
        foreach ($d as $c) {
            //echo "DELETING: {$c['id']}<br />";
            Iscrizioni::delete($c['id']);            
        }
        echo "iscrizioni duplicate eliminate";
    }
    
    ##
    public function subscribeAction(){
        $app = App::getInstance();
        $data = $_POST;
        
        $lista = $data["lista"];
        $email = isset($data['email'])?$data["email"]:"";
        $nome = isset($data['nome'])?$data["nome"]:"";
        $cognome = isset($data['cognome'])?$data['cognome']:"";
        $privacy = isset($data['privacy']) && $data['privacy'] == 1? 1 : 0;
        
        $id_c = Contact::submit(array(
                'nome'          => mysql_real_escape_string($nome),
                'cognome'       => mysql_real_escape_string($cognome),
                'type'          => 'html',
                'email'         => mysql_real_escape_string($email),
                'iscritto'      => MYSQL_NOW(),
                'lastedit'      => MYSQL_NOW(),
                'privacy'       => mysql_real_escape_string($privacy),
                
            ));
        Iscrizioni::submit(array(
                'lista_id'          =>  mysql_real_escape_string($lista),
                'contatto_id'       =>  $id_c->id,
                'creata'            => MYSQL_NOW(),                
            ));
        
		$app->redirect(@$data["return"]);
    }
    
    ##
    public function cronAction(){
        echo "in sviluppo!";
    }
    
    ##
    public function processAction(){
        
        $tot = isset($_POST['tot'])?$_POST['tot']:1;
        $cur = isset($_POST['curr'])?$_POST['curr']:1;
        $limit = isset( $_POST['limit'] ) && $_POST['limit'] > 1 ? $_POST['limit'] : 1;
        
        $current =  $tot - $cur;
        $prog = $current * 100 / $tot;
        $reply = '';
        $toSends = Coda::query(
                array(
                    'processato'  => 0,
                    'limit'       => $limit,
                ));
        $server = AccountSMTP::findServer();
        if (!$server){$reply ="non ci sono server". "\r\n";}
        if (!isset($server->host)){$reply = "il server non ha un host valido". "\r\n";}
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $server->host;
        $mail->SMTPAuth = true;
        $mail->Username = $server->username;
        $mail->Password = $server->password;
        $mail->SMTPSecure = $server->connection;
        $mail->Port = $server->port;
        $mail->SMTPKeepAlive = true;
        $mail->setFrom($server->sender_mail, $server->sender_name);
         
        foreach ($toSends as $dest){
            $contact = Contact::load($dest->contact_id);
            //var_dump($contact);
            if (!$contact->email || $contact->id==null){ 
                $dest->server_id = $server->id;
                $dest->processato = 1;
                $dest->note = "Email del contatto {$contact->id} assente! O Contatto non presente!";
                $dest->store();
                $reply = "Email del contatto {$contact->id} assente! O Contatto non presente!" . "\r\n";
                Contact::delete($contact->id);
                continue;
            }
            $mail2 = clone $mail;
            $mail2->addAddress($contact->email, $contact->nome.' '.$contact->cognome);
            $mail2->isHTML(true);
            $email = Email::load($dest->email_id);
            
            $find = array('{nome}','{cognome}','{confermaPrivacy}','{deleteContact}');
            $change = array(
                $contact->nome,
                $contact->cognome,
                __HOME__."/remote/activePrivacy/token/".$contact->token,
                __HOME__."/remote/deActive/token/".$contact->token
            );
            /*
            $append_html = "se vuoi cancellarti dalle nostre banche dati clicca il link nel tuo browser <a href='".__HOME__."/remote/deActive/token/{$contact->token}' target='_blank'>".__HOME__."/remote/deActive/token/{$contact->token}</a>";
            $append_text = "se vuoi cancellarti dalle nostre banche dati copia e incolla il link nel tuo browser ".__HOME__."/remote/deActive/token/{$contact->token}";
            */
            $mail2->Subject = $email->oggetto;
            $mail2->Body    = str_replace($find,$change,$email->messaggio_html);
            $mail2->AltBody = str_replace($find,$change,$email->messaggio_text);
            $mail2->addReplyTo($server->replyTo);
            
            if(!$mail2->send()) {
                $reply .= 'Errore per '.$contact->email.' info: '.$mail2->ErrorInfo."\r\n";
                $dest->note = $mail2->ErrorInfo;
                $dest->server_id = $server->id;
            } else {
                $reply .= $contact->email." Invio avvenuto con successo..." . "\r\n";
                $dest->note = "Invio OK";
                $dest->execute = MYSQL_NOW();                
                $dest->server_id = $server->id;
                $dest->processato = 1;
                $email->execute = MYSQL_NOW();
                $server->send ++;
                $server->total_send ++;
                $server->perc = $server->send * 100 / $server->max_mail_day;
                $server->last_send = MYSQL_NOW();                
            }
            if ($server->max_mail_day == $server->send) {
                $server->active = 0;
            } 
            $email->store();
            $dest->store();
            $server->store();
            if (!$server->active){
                $mail->SmtpClose();
                $server = SendController::findServer();
                if (!$server){$reply ="non ci sono server". "\r\n";continue;}
                if (!isset($server->host)){$reply = "il server non ha un host valido". "\r\n";continue;}
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host = $server->host;
                $mail->SMTPAuth = true;
                $mail->Username = $server->username;
                $mail->Password = $server->password;
                $mail->SMTPSecure = $server->connection;
                $mail->Port = $server->port;
                $mail->SMTPKeepAlive = true;
                $mail->setFrom($server->sender_mail, $server->sender_name);
                $reply .= "Cambiato Server nuovo server:".$server->code."\r\n";
            }
        }
        //$reply .= 'Processo terminato con successo';
        $json = array(
            'progress' => $prog,
            'remain' => $tot-$current-1,
            'message' => $reply,
        );
        echo json_encode($json);
    }

}