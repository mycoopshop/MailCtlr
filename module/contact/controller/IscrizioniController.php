<?php

require_once __BASE__.'/module/contact/grid/IscrizioniGrid.php';
require_once __BASE__.'/module/contact/grid/DettaglioGrid.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Lista.php';
require_once __BASE__.'/module/contact/model/Contact.php';

class IscrizioniController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $grid = new IscrizioniGrid();
        $app->render([
            'title'        => 'Iscrizioni',
            'createUrl'    => __HOME__.'/iscrizioni/create',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function createAction()
    {
        $app = App::getInstance();
        $item = new Iscrizioni();
        $item->creata = MYSQL_NOW();
        $id = (int) $app->getUrlParam('id');
        if (isset($id) && $id > 0) {
            $item->lista_id = $id;
        }
        $app->render([
            'title'        => 'Nuova Iscrizione',
            'closeUrl'     => __HOME__.'/iscrizioni',
            'item'         => $item,
        ]);
    }

    ##

    public function detailAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Iscrizioni::load($id);
        $app->render([
            'title'     => 'Dettaglio Iscrizioni',
            'modifyUrl' => __HOME__.'/iscrizioni/modify/id/'.$id,
            'item'      => $item,
        ]);
    }

    ##

    public function modifyAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Iscrizioni::load($id);
        $app->render([
            'title'   => 'Modifica Iscrizione',
            'item'    => $item,
        ]);
    }

    ##

    public function saveAction()
    {
        $app = App::getInstance();
        $item = Iscrizioni::build($_POST);
        $item->user_id = $app->user['id'];
        $item->store();
        $app->redirect(__HOME__.'/iscrizioni/');
    }

    ##

    public function gridAction()
    {
        $grid = new IscrizioniGrid();
        echo json_encode($grid->json());
    }

    ##

    public function deleteAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        Iscrizioni::delete($id);
        $app->redirect(__HOME__.'/iscrizioni/');
    }

    ##

    public function formIscrizioneAction()
    {
        $app = App::getInstance();

        $id = (int) $app->getUrlParam('id');
        $lista = Lista::load($id);

        $app->render([
            'title'    => 'Form raccolta email',
            'lista'    => $lista,
        ]);
    }

    ##

    public function dettaglioAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $grid = new DettaglioGrid($id);
        $app->render([
            'title'        => 'Dettaglio Lista',
            'createUrl'    => __HOME__.'/iscrizioni/create/id/'.$id,
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function griddettaglioAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $grid = new DettaglioGrid($id);
        echo json_encode($grid->json());
    }
}
