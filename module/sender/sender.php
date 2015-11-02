<?php

##
class SenderModule {
	 
	##
	public function __construct() {
		
		##
		$app = App::getInstance();

		##
		$app->addMenu('navbar',array(
			'id'	=> 'navbar-mail',
			'label'	=> 'Mail',
		));
		
        ##
        $app->addMenu('navbar',array(
			'id'	=> 'navbar-job',
			'label'	=> 'Job',
		));
		
        
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-mail',
            'label'		=> 'Elenco Mail',
            'link'		=> __HOME__.'/email/'
        ));			
		
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-mail',
            'label'		=> 'Aggiungi Mail',
            'link'		=> __HOME__.'/email/create'
        ));			
        
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-job',
            'label'		=> 'In Corso',
            'link'		=> __HOME__.'/send/'
        ));			
		
        ##
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-job',
            'label'		=> 'Nuovo',
            'link'		=> __HOME__.'/send/create'
        ));
        
        //ACCESSO RAPIDO
        
        ##
        $app->addMenu('dashboard-button',array(
            'link'  => __HOME__.'/email/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Crea Nuova Email',
        ));
        
        ##
        $app->addMenu('dashboard-button',array(
            'link'  => __HOME__.'/send/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Aggiungi a Coda',
        ));
        
        ##
        $app->addMenu('dashboard-button',array(
            'link'  => __HOME__.'/send/live',
            'type'  => 'success',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Live Process',
        ));
        
        
        
	}
}