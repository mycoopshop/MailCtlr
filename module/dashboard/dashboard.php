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
                'label'        => _('License'),
                'link'         => __HOME__.'/dashboard/license',
            ]);
    }
}
