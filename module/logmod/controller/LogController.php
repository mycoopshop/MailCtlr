<?php

require_once __BASE__.'/module/logmod/grid/LogGrid.php';
require_once __BASE__.'/module/logmod/model/Log.php';

class LogController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();
        $grid = new LogGrid();
        //Log::drop();
        /*
        $info_user = User::getInfoUser();
        $log = array(
            'action' => "Lettura del registro log", //azione fatta
            'data' => MYSQL_NOW(),
            'user_id' => $app->user['id'],
            'user_ip' => $info_user->ip,
            'user_hostname' =>  $info_user->hostname,
            'user_city' => $info_user->city,
            'user_region' => $info_user->region,
            'user_country' => $info_user->country,
            'user_loc' => $info_user->loc,
            'user_org' => $info_user->org,
        );
        //Log::submit($log);
        */
        //Log::logga("Test","Testo il sistema di log 'Avanzato'!");

        $app->render([
            'title'        => 'Log di Sisitema',
            'grid'         => $grid->html(),
        ]);
    }

    ##

    public function gridAction()
    {
        $grid = new LogGrid();
        echo json_encode($grid->json());
    }

    public function detailAction()
    {

        ##
        $app = App::getInstance();

        ##
        $id = (int) $app->getUrlParam('id');

        ##
        $item = Log::load($id);

        $app->render([
            'title'        => 'Log di Sisitema',
            'item'         => $item,
        ]);
    }
}
