<?php

require_once __BASE__.'/module/changelog/model/Changelog.php';

class ChangelogController
{
    ##

    public function indexAction()
    {
        $app = App::getInstance();

        $app->render([
            'title'        => 'Storia delle versioni',
            'changelog'    => Changelog::all(),
            'current'      => __VERSION__,
        ]);
    }

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

    public function changelogJsonAction()
    {
        echo json_encode(Changelog::all());
    }
}
