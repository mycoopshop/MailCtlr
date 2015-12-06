<?php

require_once __BASE__.'/module/contact/grid/ContactGrid.php';
require_once __BASE__.'/module/contact/files/ContactFiles.php';
require_once __BASE__.'/module/contact/grid/ContactCheckedGrid.php';
require_once __BASE__.'/module/contact/grid/ContactModalGrid.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/contact/lib/parsecsv.lib.php';
require_once __BASE__.'/module/contact/lib/checksmtp.lib.php';

class ContactController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $grid = new ContactGrid();
        $app->render([
            'title'        => _('Contact'),
            'createUrl'    => __HOME__.'/contact/create',
            'import'       => __HOME__.'/contact/import',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function checkedOkAction()
    {
        $app = App::getInstance();
        $grid = new ContactCheckedGrid();
        $app->render([
            'title'        => _('Verified Contact'),
            'createUrl'    => __HOME__.'/contact/create',
            'importCSV'    => __HOME__.'/contact/importCSV',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function gridCheckedAction()
    {
        $grid = new ContactCheckedGrid();
        echo json_encode($grid->json());
    }

    ##

    public function createAction()
    {
        $app = App::getInstance();
        $item = new Contact();
        $item->iscritto = MYSQL_NOW();
        $item->lastedit = MYSQL_NOW();

        $app->render([
            'title'        => _('New contact'),
            'closeUrl'     => __HOME__.'/contact',
            'item'         => $item,
        ]);
    }

    ##

    public function detailAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Contact::load($id);
        $app->render([
            'title'     => _('User detail'),
            'modifyUrl' => __HOME__.'/contact/modify/id/'.$id,
            'item'      => $item,
        ]);
    }

    ##

    public function modifyAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Contact::load($id);
        $item->lastedit = MYSQL_NOW();
        $app->render([
            'title'   => _('Edit contact'),
            'item'    => $item,
        ]);
    }

    ##

    public function saveAction()
    {
        $app = App::getInstance();
        $item = Contact::build($_POST);
        /*
         * Verify format contact and save if is correct
         * redirect to create page if format don't is correct
         * 
         */
        if (Contact::checkContact($item->email)) {
            $item->user_id = $app->user['id'];
            $item->store();
            $app->redirect(__HOME__.'/contact/');
        } else {
            $app->redirect(__HOME__.'/contact/create/msg/non%20valido!');
        }
    }

    ##

    public function gridAction()
    {
        $grid = new ContactGrid();
        echo json_encode($grid->json());
    }

    ##

    public function deleteAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        Contact::delete($id);
        $app->redirect(__HOME__.'/contact/');
    }

    ##

    public function filesAction()
    {
        $files = new ContactFiles();
        echo $files->response();
    }

    ##

    public function importAction()
    {
        $app = App::getInstance();
        $app->render([
            'title'     => _('Import contact form csv file'),
            'user'      => $app->user['id'],
            'createUrl' => __HOME__.'/contact/import',
        ]);
    }

    /*
     * Import action:
     *      format csv file for correct working
     *      nome;cognome;email;
     */
    ##

    public function importCSVAction()
    {
        $reply = '';
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $csv = new parseCSV();
        $f = ContactFiles::load($id);
        $csv->auto($f->getPath());
        $reply .= 'START<br />';
        $count = 0;
        foreach ($csv->data as $i => $info) {
            $email = $info{'email'};
            $nome = isset($info{'nome'}) ? $info{'nome'} : '';
            $cognome = isset($info{'cognome'}) ? $info{'cognome'} : '';

            if (!Contact::checkContact($email)) {
                $reply .= $email.' contatto non valido!<br />';
                continue;
            }
            $id_c = Contact::submit([
                'nome'     => $nome,
                'cognome'  => $cognome,
                'email'    => $email,
                'active'   => 1,
                'privacy'  => 0,
                'iscritto' => MYSQL_NOW(),
                'lastedit' => MYSQL_NOW(),
                'type'     => 'html',
            ]);
            $t = Contact::makeToken($id_c->id);
            $reply .= "Contatto: $nome $cognome $email Token: $t<br />";
            $count++;
        }
        $reply .= 'END';
        ContactFiles::delete($f->id);

        //echo $reply;
        $app->render([
            'title'     => _("Import contact from csv [imported contact: $count]"),
            'createUrl' => __HOME__.'/contact/import',
            'reply'     => $reply,
        ]);
    }

    ##

    public function modalSearchAction()
    {
        $grid = new ContactModalGrid();
        echo $grid->html();
    }

    ##

    public function modalGridJsonAction()
    {
        $grid = new ContactModalGrid();
        echo json_encode($grid->json());
    }

    ##

    public function renderAction()
    {
        $item = Contact::load($_POST['id']);
        echo json_encode($item);
    }

    ##

    public function liveCheckAction()
    {
        $app = App::getInstance();
        $app->appendJs(__HOME__.'/module/contact/js/process.js');

        $tot = Contact::count('verify0');
        $num = $tot / 1;
        $liste = Lista::all();
        $email = Email::all();

        $app->render([
            'totale'    => $num,
            'number'    => $num,
            'action'    => __HOME__.'/remote/cleanContact',
            'liste'     => $liste,
            'email'     => $email,
        ]);
    }
}
