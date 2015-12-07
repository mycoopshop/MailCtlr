<?php

require_once __BASE__.'/module/config/grid/OptionsGrid.php';
require_once __BASE__.'/module/config/model/Options.php';

class OptionsController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $grid = new OptionsGrid();
        $app->render([
            'title'        => _('Setting'),
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
            'title'        => _('New Setting'),
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
            'title'     => _('Setting Detail'),
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
            'title'   => _('Edit Setting'),
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
