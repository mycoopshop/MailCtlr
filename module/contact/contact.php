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
            'label'    => _('Contact'),
        ]);

        ##
        $app->addMenu('navbar', [
            'id'       => 'navbar-list',
            'label'    => _('List'),
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => _('List Contact'),
            'link'         => __HOME__.'/contact/',
        ]);
 
        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => _('Add contact'),
            'link'         => __HOME__.'/contact/create',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => _('Import contact'),
            'link'         => __HOME__.'/contact/import',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-contact',
            'label'        => _('Clear contact'),
            'link'         => __HOME__.'/contact/liveCheck',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => _('List'),
            'link'         => __HOME__.'/lista/',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => _('Add list'),
            'link'         => __HOME__.'/lista/create',
        ]);

        ##
        $app->addMenu('navbar', [
            'parent'       => 'navbar-list',
            'label'        => _('Subscribers'),
            'link'         => __HOME__.'/iscrizioni/',
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/lista/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => _('New list'),
        ]);

        ##
        $app->addMenu('dashboard-button', [
            'link'  => __HOME__.'/contact/create',
            'type'  => 'default',
            'icon'  => 'glyphicon glyphicon-plus',
            'label' => _('New contact'),
        ]);
    }
}
