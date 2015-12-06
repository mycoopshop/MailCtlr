<?php

##
class ContactModule
{
    ##

    public function __construct()
    {

        ##
        $app = App::getInstance();

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-contact',
            'label'    => 'Contatti',
        ]);

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-list',
            'label'    => 'Liste',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => 'Tutti i Contatti',
            'link'         => __HOME__.'/contact/',
        ]);

        ##
        /*
        $app->addMenu('navbar',array(
            'parent'	=> 'navbar-contact',
            'label'		=> 'Contatti Verificati',
            'link'		=> __HOME__.'/contact/checkedOk'
        ));
        */
        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => 'Aggiungi Contatti',
            'link'         => __HOME__.'/contact/create',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => 'Importa Contatti',
            'link'         => __HOME__.'/contact/import',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => 'Pulisci Contatti',
            'link'         => __HOME__.'/contact/liveCheck',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => 'Elenco Liste',
            'link'         => __HOME__.'/lista/',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => 'Aggiungi Liste',
            'link'         => __HOME__.'/lista/create',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => 'Iscrizioni',
            'link'         => __HOME__.'/iscrizioni/',
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/lista/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Nuova Lista',
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/contact/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => 'Nuovo Contatto',
        ]);
    }
}
