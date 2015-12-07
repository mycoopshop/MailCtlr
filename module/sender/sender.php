<?php

##
class SenderModule
{
    ##

    public function __construct()
    {

        ##
        $app = App::getInstance();

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-mail',
            'label'    => _('Mail'),
        ]);

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-job',
            'label'    => _('Proccesses'),
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-mail',
            'label'        => _('List mail'),
            'link'         => __HOME__.'/email/',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-mail',
            'label'        => _('Add mail'),
            'link'         => __HOME__.'/email/create',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-job',
            'label'        => _('In progress'),
            'link'         => __HOME__.'/send/',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-job',
            'label'        => _('New'),
            'link'         => __HOME__.'/send/create',
        ]);

        $app->addMenu('navbar', [
            'parent'       => 'navbar-job',
            'label'        => _('Processed'),
            'link'         => __HOME__.'/send/processate',
        ]);
        //ACCESSO RAPIDO

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/email/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => _('Create new email'),
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/send/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => _('Add queue'),
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/send/live',
            'type'  => 'success',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => _('Live process'),
        ]);
    }
}
