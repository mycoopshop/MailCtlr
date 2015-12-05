<?php

require_once __BASE__.'/module/config/model/AccountSMTP.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';
require_once __BASE__.'/module/sender/model/Coda.php';
##
class DashboardController
{
    ##

    public function indexAction()
    {

        ##
        $app = App::getInstance();

        $max_mail = AccountSMTP::getMaxMail();
        $total = AccountSMTP::getSenderMail();
        $remain = AccountSMTP::getRemainMail();
        $liste = Lista::count();
        $contatti = Contact::count();
        $toSend = Coda::attendSend();

        ##
        $app->render([
            'max_mail'        => $max_mail,
            'total'           => $total,
            'tosend'          => $toSend,
            'remain'          => $remain,
            'liste'           => $liste,
            'contatti'        => $contatti,
            ]
        );
    }

    ##

    public function testAction()
    {
        $app = App::getInstance();
        $app->render();
    }

    ##

    public function licenseAction()
    {
        $app = App::getInstance();
        $app->render();
    }
}
