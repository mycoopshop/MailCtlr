<?php

##
class ContactModule {
	 
	##
	public function __construct() {
		
		##
		$app = App::getInstance();

		##
		$app->addMenu('navbar',array(
			'id'	=> 'navbar-contact',
			'label'	=> 'Contatti',
		));
		
        ##
        $app->addMenu('navbar',array(
			'id'	=> 'navbar-list',
			'label'	=> 'Liste',
		));
		
        ##
        $app->addMenu('navbar',array(
            //'parent'	=> 'navbar-contact',
            'label'		=> 'Iscrizioni',
            'link'		=> __HOME__.'/iscrizioni/'
        ));
        
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-contact',
            'label'		=> 'Elenco Contatti',
            'link'		=> __HOME__.'/contact/'
        ));			
		
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-contact',
            'label'		=> 'Aggiungi Contatti',
            'link'		=> __HOME__.'/contact/create'
        ));			
        
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-list',
            'label'		=> 'Elenco Liste',
            'link'		=> __HOME__.'/lista/'
        ));			
		
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-list',
            'label'		=> 'Aggiungi Liste',
            'link'		=> __HOME__.'/lista/create'
        ));
        
        
        
        ##
        $app->addMenu('dashboard-button',array(
            'link'  => __HOME__.'/lista/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Nuova Lista',
        ));
        
        ##
        $app->addMenu('dashboard-button',array(
            'link'  => __HOME__.'/contact/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Nuovo Contatto',
        ));
        
        
	}
}