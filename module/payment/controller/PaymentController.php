<?php
require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/sender/controller/SendController.php';

class PaymentController {
    
    ##
	public function indexAction() {		
        $info = (object) array();
        
        $info->currency = "EUR";
        $info->lc = "IT";
        $info->business = "pagamenti-facilitator@ctlr.it";
        $info->notify = "http://www.ctlr.eu/MailCtlr/payment/readIPN/";
        $info->discount_rate = 40;
        $info->quantity = 1;
        $info->tax_rate = 22;
        $info->amount = 109;
        $info->item_name = "Testing PayPal Reply";
        $info->item_number = "A001";
        
        
        echo '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">'
        .  '<input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="currency_code" value="'.$info->currency.'" />
            <input type="hidden" name="lc" value="'.$info->lc.'" />
            <input type="hidden" name="business" value="'.$info->business.'" />
            <input type="hidden" name="notify_url" value="'.$info->notify.'" />
            
            <input type="hidden" name="item_name" value="'.$info->item_name.'" />
            <input type="hidden" name="discount_rate" value="'.$info->discount_rate.'" />
            <input type="hidden" name="item_number" value="'.$info->item_number.'" />
            <input type="hidden" name="quantity" value="'.$info->quantity.'" />
            <input type="hidden" name="tax_rate" value="'.$info->tax_rate.'" />
            <input type="hidden" name="amount" value="'.$info->amount.'" />
            
            <input type="submit" value="PAGA ADESSO" />
        </form>';
        
        echo "<pre>";
        var_dump($info);
	}
    public function readIPNAction(){
        
        $html = "<pre>".print_r($_POST, true);
        
        $server = SendController::findServer();
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $server->host;
        $mail->SMTPAuth = true;
        $mail->Username = $server->username;
        $mail->Password = $server->password;
        $mail->SMTPSecure = $server->connection;
        $mail->Port = $server->port;
        $mail->setFrom($server->sender_mail, $server->sender_name);
         
        $mail->addAddress('vincenzo@ctlr.it', 'Vincenzo La Rosa');
        $mail->isHTML(true);

        $mail->Subject = 'TEST IPN PAYPAL';
        $mail->Body    = $html;
        $mail->AltBody = 'TESTING IPN PAYPAL';

        if(!$mail->send()) {
            echo "ERRORE EMAIL";
        } else {
            $server->send ++;
            $server->total_send ++;
            $server->last_send = MYSQL_NOW();                
        }
        $server->store();
        
    }
    
    
}