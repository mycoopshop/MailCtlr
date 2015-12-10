<?php

##
class ConfigModule
{
    ##
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

        ##
        $app = App::getInstance();

        ##
        /*$app->addMenu('navbar',array(
            'id'	=> 'navbar-report',
            'label'	=> _('Statistics'),
        ));*/

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-config',
            'label'    => _('Configuration'),
        ]);

        ##
        if ($app->testAcl('menu-user', $this->acl)) {
        }

        ##
        if ($app->testAcl('menu-admin', $this->acl)) {
            $app->addMenu('navbar', [
                'parent'       => 'navbar-config',
                'label'        => _('Manager server SMTP'),
                'link'         => __HOME__.'/accountSMTP/',
            ]);

            ##
            $app->addMenu('dashboard-button', [
                'link'  => __HOME__.'/accountSMTP',
                'type'  => 'success',
                'icon'  => 'glyphicon glyphicon-plus',
                'label' => _('Server SMTP'),
            ]);

            $app->addMenu('navbar', [
                'parent'       => 'navbar-config',
                'label'        => _('Setting'),
                'link'         => __HOME__.'/options/',
            ]);
        }

        ##
        if ($app->testAcl('menu-super', $this->acl)) {

            ##
            $app->addMenu('navbar', [
                'parent'       => 'navbar-config',
                'label'        => _('User Manager'),
                'link'         => __HOME__.'/user/',
            ]);
        }
    }
}
