<?php

class DevnoteModule {	

    private $acl = array(
        'superadmin'=>array(
            'menu-footer'            => true,
        ),
		'admin' => array(
            'menu-footer'            => true,
		),
		'user' => array(
            'menu-footer'            => false,
		),
    );
    
    public function __construct() {
		$app = App::getInstance();
                
        if ($app->testAcl('menu-footer',$this->acl)) {

            $app->addMenu('footer-link',array(
                    'label' => 'Help',
                    'link'	=> __HOME__.'/devnote/ticket',
            ));

        }
	}
}