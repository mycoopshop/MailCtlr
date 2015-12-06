<?php

class DevnoteModule
{
    private $acl = [
        'superadmin' => [
            'menu-footer'            => true,
        ],
        'admin' => [
            'menu-footer'            => true,
        ],
        'user' => [
            'menu-footer'            => false,
        ],
    ];

    public function __construct()
    {
        $app = App::getInstance();

        if ($app->testAcl('menu-footer', $this->acl)) {
            $app->addMenu('footer-link', [
                    'label'   => 'Help',
                    'link'    => __HOME__.'/devnote/ticket',
            ]);
        }
    }
}
