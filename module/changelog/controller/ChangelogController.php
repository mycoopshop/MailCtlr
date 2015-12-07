<?php

require_once __BASE__.'/module/changelog/model/Changelog.php';

class ChangelogController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();

        $app->render([
            'title'        => _('List application release'),
            'changelog'    => Changelog::all(),
            'current'      => __VERSION__,
        ]);
    }

    /*
     * Check update from remote repository
     *      function to review
    */
    ##

    public function updateChangelogAction()
    {
        $json = file_get_contents(__HOME__.'/changelog/changelogJson');
        $data = json_decode($json);
        $i = 1;
        foreach ($data as $changelog) {
            $cl = new Changelog();
            $cl->store($changelog);
            $i++;
        }
        echo "Inseriti {$i} cambiamenti!";
    }

    /*
     * Function for get all change in json
    */
    ##

    public function changelogJsonAction()
    {
        echo json_encode(Changelog::all());
    }
}
