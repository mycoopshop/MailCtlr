<?php

require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';

require_once __BASE__.'/module/sender/grid/SendGrid.php';
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
        
        
        $number = count ( Coda::all() ) / 50;
        //print($number);die();
        
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
    public function liveAction(){   
        $app = App::getInstance();
        
        $app->appendJs(__HOME__.'/module/sender/js/process.js');
                
        $number = count ( Coda::all() ) / 50;
        
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
        //$app = App::getInstance();
        $limit = isset( $_POST['limit'] ) && $_POST['limit'] > 50 ? $_POST['limit'] : 50;
        $toSends = Coda::query(
                array(
                    'processato'  => 0,
                    'limit'       => $limit,
                ));
        
        $server = SendController::findServer();
        if (!$server){die("non ci sono server");}
        if (!isset($server->host)){die("il server non ha un host valido");}
        
        //var_dump($server);die();
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
            $mail->addAddress($contact->email, $contact->nome.' '.$contact->cognome);
            $mail->isHTML(true);
            $email = Email::load($dest->email_id);
            
            $mail->Subject = $email->oggetto;
            $mail->Body    = $email->messaggio_html;
            $mail->AltBody = $email->messaggio_text;
            
            if(!$mail->send()) {
                $reply .= 'Errore per '.$contact->email.' info: '.$mail->ErrorInfo."\n\r";
                $dest->note = $mail->ErrorInfo;
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
                $server->perc = $server->send * 100 / $server->max_mail;
                $server->last_send = MYSQL_NOW();                
            }
            $dest->store();
            
            if ($server->max_mail == $server->send) {
                $server->active = 0;
            }            
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
        //$app->redirect(__HOME__.'/send/');
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
    public static function activeServerAction(){ //azzera giornalmente i conteggi
        $servers = AccountSMTP::all(); 
        /*$servers = AccountSMTP::query(
            array(
                'active'  => 0,
            ));*/
        echo '<pre>';
        foreach ($servers as $server){
            $diff = abs(strtotime(MYSQL_NOW()) - strtotime($server->last_send));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $day = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            switch ($server->ever) {
                case 'day':
                    if ($day >= DAY ){ 
                        $server->active = 1;
                        $server->send = 0;
                        echo $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'month':
                    if ($day >= MONTH ){ 
                        $server->active = 1;
                        $server->send = 0;
                        echo $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'year':
                    if ($day >= YEAR ){ 
                        $server->active = 1;
                        $server->send = 0;
                        echo $server->id." ATTIVATO.<br />";
                    }
                    break;
                case 'week':
                    if ($day >= WEEK ){ 
                        $server->active = 1;
                        $server->send = 0;
                        echo $server->id." ATTIVATO.<br />";
                    }
                    break;
            }
            $server->store();
        } 
        echo "ATTIVATI TUTTI I SERVER.";
    }
}    
