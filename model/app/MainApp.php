<?php

require_once __BASE__.'/lib/liberty/App.php';
require_once __BASE__.'/lib/curi/curi.php';

class MainApp extends App
{
    public $acl = [
        'admin'    => [
            '*'                        => 1,
        ],
        'Super_Amministratore' => [
            '*'                        => 1,
        ],
        'public' => [
            '*'                        => 0,
            'userrole.Login.*'         => 1,
        ],
    ];

    public function __construct($params)
    {
        parent::__construct($params);
        $this->init();
    }

    public function run()
    {
        $this->load();
        $this->exec();
    }

    public function error($error)
    {
        $type = isset($error['type']) ? $error['type'] : 'undefined';

        if ($type == 'ACCESS_DENIED') {
            if (in_array('Operatore', $this->user['role'])) {
                #$app->Redirect("logger/index");
            } else {
                $this->Redirect('login/index', [
                    'redirect' => curi_get_current(), ]
                );
            }
        }

        $this->render_theme([
            'view' => $error['message'],
        ]);
    }
}
