<?php

require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';
require_once __BASE__.'/module/sender/lib/checkContact.php';
require_once __BASE__.'/module/sender/grid/SendGrid.php';
require_once __BASE__.'/module/sender/grid/ProcessateGrid.php';
require_once __BASE__.'/module/sender/model/Coda.php';
require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';

class SendController{
    
    public function indexAction(){   
        $app = App::getInstance();		
		$grid = new SendGrid();
        $app->appendJs(__HOME__.'/module/sender/js/process.js');      
        
        $number = count ( Coda::all() ) / 30;
        
		$app->render(array(
			'title'		=> 'Coda Email da Inviare',
			'createUrl' => __HOME__.'/send/create',
            'realTime'  => __HOME__.'/send/live',
            'modal'     => '#processModal',
            'number'    => $number,
            'action'    => __HOME__.'/send/process',
			'grid'		=> $grid->html(),
		));
    }
    public function processateAction(){   
        $app = App::getInstance();		
		$grid = new ProcessateGrid();
        
		$app->render(array(
			'title'		=> 'Coda Email da Inviare',
			'createUrl' => __HOME__.'/send/create',
            'grid'		=> $grid->html(),
		));
    }
    public function liveAction(){   
        $app = App::getInstance();
        
        $app->appendJs(__HOME__.'/module/sender/js/process.js');
                
        $number = count ( Coda::all() ) / 30;
        
		$app->render(array(
			'title'		=> 'Invio email in tempo reale',
            'modal'     => '#processModal',
            'number'    => $number,
            'action'    => __HOME__.'/send/process',
		));
        
    }
    public function createAction() {		
		$app = App::getInstance();				
		$app->render(array(
			'title'		=> 'Nuovo Utente',
			'closeUrl'	=> __HOME__.'/send',
			'item'		=> new User(),
		));
    }    
    public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = User::load($id);		
		$app->render(array(
			'title' => 'Dettaglio Utente',
			'modifyUrl' => __HOME__.'/send/modify/id/'.$id,
			'item'	=> $item,			
		));
    }
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		Coda::delete($id);		
		$app->redirect(__HOME__.'/send/');
    }
    public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = User::load($id);
		$item->password = "";		
		$app->render(array(
			'title' => 'Modifica Utente',
			'item'	=> $item,			
		));
	}
    public function saveAction() {		
		$app = App::getInstance();		
		
        Coda::addCode($_POST['lista'], $_POST["email"]);
		$app->redirect(__HOME__.'/send/');
    }
    public function gridAction() {		
		##
		$grid = new SendGrid();		
		##
		echo json_encode($grid->json());			
    }
    public function processategridAction() {		
		##
		$grid = new ProcessateGrid();		
		##
		echo json_encode($grid->json());			
    }
    public function renderAction() {		
		
        $item = Coda::load($_POST['id']);		
		echo json_encode($item);
    }
    public function stateAction(){   
        $app = App::getInstance();		
				
		$app->render(); 			
    }
    ##
    public function processAction(){
        $reply = '';
        $limit = isset( $_POST['limit'] ) && $_POST['limit'] > 30 ? $_POST['limit'] : 30;
        $toSends = Coda::query(
                array(
                    'processato'  => 0,
                    'limit'       => $limit,
                ));
        $server = SendController::findServer();
        if (!$server){die("non ci sono server");}
        if (!isset($server->host)){die("il server non ha un host valido");}
        
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
            if (!$contact->email){ 
                $dest->server_id = $server->id;
                $dest->processato = 1;
                $email->execute = MYSQL_NOW();
                $dest->note = "Email del contatto <b>{$contact->id}</b> assente!";
                $dest->store();
                die("Email del contatto <b>{$contact->id}</b> assente!"); 
            }
            $mail2 = clone $mail;
            $mail2->addAddress($contact->email, $contact->nome.' '.$contact->cognome);
            $mail2->isHTML(true);
            $email = Email::load($dest->email_id);
            
            $append_html = "se vuoi cancellarti dalle nostre banche dati clicca il link nel tuo browser <a href='".__HOME__."/remote/deActive/hask/{$contact->hask}' target='_blank'>".__HOME__."/remote/deActive/hask/{$contact->hask}</a>";
            $append_text = "se vuoi cancellarti dalle nostre banche dati copia e incolla il link nel tuo browser ".__HOME__."/remote/deActive/hask/{$contact->hask}";
            
            $mail2->Subject = $email->oggetto;
            $mail2->Body    = $email->messaggio_html; //.$append_html;
            $mail2->AltBody = $email->messaggio_text; //.$append_text;
            
            if(!$mail2->send()) {
                $reply .= 'Errore per '.$contact->email.' info: '.$mail2->ErrorInfo."\n\r";
                $dest->note = $mail2->ErrorInfo;
                $dest->server_id = $server->id;
            } else {
                $reply .= $contact->email.' Invio avvenuto con successo '."\n\r";
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
                if (!$server){die("non ci sono server");}
                if (!isset($server->host)){die("il server non ha un host valido");}
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
                $reply .= "Cambiato Server nuovo server:".$server->code."\n\r";
            }
        }
        $reply .= 'Processo terminato con successo';
        echo $reply.'<br />';
    }
    ##
    public function testServerAction(){
        $reply = '';
        $data = AccountSMTP::load(5);
        //$data = $_POST[];
        $debug = '';
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $data->host;
        $mail->SMTPAuth = true;
        $mail->Username = $data->username;
        $mail->Password = $data->password;
        $mail->SMTPSecure = $data->connection;
        $mail->Port = $data->port;
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = function($str, $level) {echo "$str<br />";};     
        
        
        if( $mail->smtpConnect() ){
            $mail->smtpClose();
            $reply.= "OK";
        }else{
            $reply.= $debug."<br />Connection Failed";
        }
        
        echo $reply;
    }
    ##
    public function dropCodaAction(){
        Coda::drop();
        echo "Coda Svuotata";
    }
    ##
    public static function findServer(){
        $servers = AccountSMTP::query(
                array(
                    'active'  => 1,
                ));
        $use = null;
        $perc = 110;
        foreach ($servers as $server){
            if ( $server->perc < $perc ){
                $use = $server;
                $perc = $use->perc;
            }
            if ($server->perc == 0) {
                return $server;
            }
        }
        return $use;
    }
    
    ##
    public function noticePrivacyAction(){
        $server = SendController::findServer();
        if (!$server){die("non ci sono server");}
        if (!isset($server->host)){die("il server non ha un host valido");}
        
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
        
        $cs = Contact::query(
                array(
                    'privacy' => 0,
                ));
        $reply = "";
        foreach ($cs as $contact){
            if (!$contact->email){ 
                /*$dest->server_id = $server->id;
                $dest->processato = 1;
                $email->execute = MYSQL_NOW();
                $dest->note = "Email del contatto <b>{$contact->id}</b> assente!";
                $dest->store();*/
                die("Email del contatto <b>{$contact->id}</b> assente!"); 
            }
            $mail2 = clone $mail;
            $mail2->addAddress($contact->email, $contact->nome.' '.$contact->cognome);
            $mail2->isHTML(true);
            $email = Email::load(9);
            
            $find = array('{nome}','{cognome}','{confermaPrivacy}','{deleteContact}');
            $change = array(
                $contact->nome,
                $contact->cognome,
                __HOME__."/remote/activePrivacy/hask/".$contact->hask,
                __HOME__."/remote/deActive/hask/".$contact->hask
            );
            
            $mail2->Subject = $email->oggetto;
            $mail2->Body    = str_replace($find,$change,$email->messaggio_html);
            $mail2->AltBody = str_replace($find,$change,$email->messaggio_text);
            
            if(!$mail2->send()) {
                $reply .= 'Errore per '.$contact->email.' info: '.$mail2->ErrorInfo."\n\r";
                $dest->note = $mail2->ErrorInfo;
                $dest->server_id = $server->id;
            } else {
                $reply .= $contact->email.' Invio avvenuto con successo '."\n\r";
                $email->execute = MYSQL_NOW();
                $server->send ++;
                $server->total_send ++;
                $server->perc = $server->send * 100 / $server->max_mail;
                $server->last_send = MYSQL_NOW();                
            }
            if ($server->max_mail == $server->send) {
                $server->active = 0;
            } 
            $email->store();
            $server->store();
            if (!$server->active){
                $mail->SmtpClose();
                $server = SendController::findServer();
                if (!$server){die("non ci sono server");}
                if (!isset($server->host)){die("il server non ha un host valido");}
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
                $reply .= "Cambiato Server nuovo server:".$server->code."\n\r";
            }
        }
        echo $reply;
        
    }
    
    ##
    public function cronAction(){
        
    }
    
    ##
    public function cleanContactAction(){
        
        $reply = '';
        $data = AccountSMTP::load(5);
        //$data = $_POST[];
        //$debug = '';
        
        $mail = new checkContact();
        $mail->isSMTP();
        $mail->Host = $data->host;
        $mail->SMTPAuth = true;
        $mail->Username = $data->username;
        $mail->Password = $data->password;
        $mail->SMTPSecure = $data->connection;
        $mail->Port = $data->port;
        
        echo $mail->checkConact("vincenzo@ctlr.it");die();
        
        $cs = Contact::all();
        foreach ($cs as $i => $c){
            
            $rix = $mail->smtp->recipient($c->email); //recipient($c->email);
            var_dump($rix);die();
        }
        
        
        echo $reply;
    }
    
    
    
    
}

