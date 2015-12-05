<?php

require_once __BASE__.'/model/app/PrivateWebApp.php';

class MailCtlrWebApp extends PrivateWebApp
{
    public $acl = [

        'public' => [
            '*'                                 => false,
            'userrole.Login.*'                  => true,
            'contact.Remote.*'                  => true,
            'install.*'                         => true,

        ],
        'user' => [
            '*'                                 => true,
            'devnote.*'                         => false,
            'config.*'                          => false,
        ],
        'admin' => [
            '*'                                 => true,
            'userrole.User.*'                   => false,
            'userrole.User.render'              => true,
        ],
        'superadmin' => [
            '*'  => true,
        ],
    ];
}
date_default_timezone_set('Europe/Rome');
ini_set('memory_limit', '2048M');
//ini_set('max_execution_time', 0);
