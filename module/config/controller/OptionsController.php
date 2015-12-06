<?php

require_once __BASE__.'/module/config/grid/OptionsGrid.php';
require_once __BASE__.'/module/config/model/Options.php';

class OptionsController
{
    ##

    public function indexAction()
    {
        $opt = [
            'name'        => 'default', //azione fatta
            'descrizione' => 'Tema + Controller Principale',
            'value'       => serialize(['theme' => 'default', 'controller' => 'Dashboard', 'action' => 'index']),
            'type'        => 'alpha',
            'last_edit'   => MYSQL_NOW(),
        ];

        //Options::submit($opt);

        $app = App::getInstance();
        $grid = new OptionsGrid();
        $app->render([
            'title'        => 'Opzioni',
            'createUrl'    => __HOME__.'/options/', //create',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function createAction()
    {
        $app = App::getInstance();
        $item = new Options();
        $item->created = MYSQL_NOW();

        $app->render([
            'title'        => 'Nuova Opzione',
            'closeUrl'     => __HOME__.'/options',
            'item'         => $item,
        ]);
    }

    ##

    public function detailAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Options::load($id);
        $app->render([
            'title'     => 'Dettaglio Opzioni',
            'modifyUrl' => __HOME__.'/options/', //modify/id/'.$id,
            'item'      => $item,
        ]);
    }

    ##

    public function modifyAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        $item = Options::load($id);
        $app->render([
            'title'   => 'Modifica Opzione',
            'item'    => $item,
        ]);
    }

    ##

    public function saveAction()
    {
        $app = App::getInstance();
        $item = Options::build($_POST);
        $item->last_edit = MYSQL_NOW();
        $item->store();
        $app->redirect(__HOME__.'/options/');
    }

    ##

    public function gridAction()
    {
        $grid = new OptionsGrid();
        echo json_encode($grid->json());
    }

    ##

    public function deleteAction()
    {
        $app = App::getInstance();
        $id = (int) $app->getUrlParam('id');
        Options::delete($id);
        $app->redirect(__HOME__.'/options/');
    }
}
