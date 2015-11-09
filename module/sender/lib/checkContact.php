<?php

class checkContact extends PHPMailer {

    
    public function checkConact($c){
        return $this->smtp->recipient($c);
    }
    
    
}
