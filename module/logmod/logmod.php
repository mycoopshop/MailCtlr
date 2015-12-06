<?php

require_once __BASE__.'/module/logmod/model/Log.php';
require_once __BASE__.'/module/userrole/model/User.php';

class LogmodModule
{
    private $acl = [

        ##
        'superadmin' => [
            'menu-super'    => true,
            'menu-admin'    => true,
            'menu-user'     => true,
        ],

        ##
        'admin' => [
            'menu-super'       => false,
            'menu-admin'       => true,
            'menu-user'        => true,
        ],

        ##
        'user' => [
            'menu-super'       => false,
            'menu-admin'       => false,
            'menu-user'        => true,
        ],
    ];

    ##

    public function __construct()
    {
        $app = App::getInstance();

        if ($app->testAcl('menu-super', $this->acl)) {
            $app->addMenu('navbar', [
                'parent'       => 'navbar-config',
                'label'        => 'Log',
                'link'         => __HOME__.'/log/',
            ]);
        }
    }
}
