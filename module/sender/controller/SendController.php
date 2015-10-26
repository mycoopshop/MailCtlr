<?php

require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';

require_once __BASE__.'/module/sender/grid/SendGrid.php';
require_once __BASE__.'/module/sender/model/Coda.php';
require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/contact/model/Lista.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';

class SendController{
    
    public function indexAction(){   
        $app = App::getInstance();		
		$grid = new SendGrid();		
		$app->render(array(
			'title'		=> 'Lista Utenti',
			'createUrl' => __HOME__.'/send/create',
			'grid'		=> $grid->html(),
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
        $reply = 'START';
        $app = App::getInstance();
        //echo "<pre>";
        $toSends = Coda::query(
                array(
                    'processato'  => 0,
                    'limit'       => 50,
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
            $mail->addAddress($contact->email, $contact->nome.' '.$contact->cognome);
            $mail->isHTML(true);
            $email   = Email::load($dest->email_id);
            
            $mail->Subject = $email->oggetto;
            $mail->Body    = $email->messaggio_html;
            $mail->AltBody = $email->messaggio_text;
            
            if(!$mail->send()) {
                $reply .= 'Errore per '.$dest->email.' info: '.$mail->ErrorInfo."\n";
                $dest->note = $mail->ErrorInfo;
            } else {
                $reply .= $dest->email.' Invio avvenuto con successo '."\n";
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
                //var_dump($server);var_dump($mail);die();
            }
        }
        //echo $reply;
        $app->redirect(__HOME__.'/send/');
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
    public static function activeServerAction(){
        $servers = AccountSMTP::query(
            array(
                'active'  => 0,
            ));
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
