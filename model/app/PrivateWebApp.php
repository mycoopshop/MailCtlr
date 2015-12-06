<?php

require_once __BASE__.'/model/app/WebApp.php';
class PrivateWebApp extends WebApp
{
    public $acl = [
        'public' => [
            '*'                    => false,
            'userrole.Login.*'     => true,
        ],
        'admin' => [
            '*' => true,
        ],
    ];

    public function __construct($file, $php_self, $request_uri)
    {
        parent::__construct($file, $php_self, $request_uri);
        global $config;
        $config['modules'][] = 'userrole';
    }

    public function accessDenied($acl)
    {
        $app = App::getInstance();
        $app->Redirect(__HOME__.'/login/');
    }
}
