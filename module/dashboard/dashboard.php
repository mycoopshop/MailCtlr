<?php

##
class DashboardModule
{
    ##

    public function __construct()
    {

        ##
        $app = App::getInstance();

        $app->addMenu('footer-link', [
                'label'        => 'Licenza',
                'link'         => __HOME__.'/dashboard/license',
            ]);
    }
}
