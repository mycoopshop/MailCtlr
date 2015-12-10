<?php

require_once __BASE__.'/module/config/grid/AccountSMTPGrid.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';

class AccountSMTPController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $grid = new AccountSMTPGrid();
        $app->render([
            'title'        => _('Server SMTP'),
            'createUrl'    => __HOME__.'/accountSMTP/create',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function createAction()
    {
        $app = App::getInstance();
        $item = new AccountSMTP();
        $item->created = MYSQL_NOW();

        $app->render([
            'title'        => _('Add Server SMTP'),
            'closeUrl'     => __HOME__.'/accountSMTP',
            'item'         => $item,
        ]);
    }

    ##

    public function detailAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = AccountSMTP::load($id);
        $app->render([
            'title'     => _('Detail Server SMTP'),
            'modifyUrl' => __HOME__.'/accountSMTP/modify/id/'.$id,
            'item'      => $item,
        ]);
    }

    ##

    public function modifyAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = AccountSMTP::load($id);
        $app->render([
            'title'   => _('Edit Server SMTP'),
            'item'    => $item,
        ]);
    }

    ##

    public function saveAction()
    {
        $app = App::getInstance();
        $item = AccountSMTP::build($_POST);

        /*
         * If replyTo is empy reply to equal sender name
        */

        $item->replyTo = isset($item->replyTo) && $item->replyTo != '' ? $item->replyTo : $item->sender_name;

        $item->last_edit = MYSQL_NOW();

        switch ($item->ever) {
            case 'day':     $item->max_mail_day = $item->max_mail / DAY; break;
            case 'month':   $item->max_mail_day = $item->max_mail / MONTH; break;
            case 'year':    $item->max_mail_day = $item->max_mail / YEAR; break;
            case 'week':    $item->max_mail_day = $item->max_mail / WEEK; break;
            case 'onetime': $item->max_mail_day = $item->max_mail; break;
        }
        $item->store();
        $app->redirect(__HOME__.'/accountSMTP/');
    }

    ##

    public function gridAction()
    {
        $grid = new AccountSMTPGrid();
        echo json_encode($grid->json());
    }

    ##

    public function deleteAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        AccountSMTP::delete($id);
        $app->redirect(__HOME__.'/accountSMTP/');
    }

    /*
     * testing connection to smtp server after save smtp account!
     *      to add at saving process
    */
    ##

    public function testServerAction()
    {
        $reply = '';
        $data = AccountSMTP::load(7);
        //$data = $_POST[];
        $debug = '';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $data->host;
        $mail->SMTPAuth = true;
        $mail->Username = $data->username;
        $mail->Password = $data->password;
        $mail->SMTPSecure = $data->connection;
        $mail->Port = $data->port;
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = function ($str, $level) {echo "$str<br />";};

        if ($mail->smtpConnect()) {
            $mail->smtpClose();
            $reply .= 'OK';
        } else {
            $reply .= $debug.'<br />'._('Connection Failed');
        }
        echo $reply;
    }
}
