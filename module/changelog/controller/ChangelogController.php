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

    ##

    public function updateAction($v = 'latest')
    {
        $u = __BASE__."/$v.zip";
        file_put_contents($u, fopen('http://www.mailctlr.org/current/latest.zip', 'r'));
        $f = new ZipArchive();
        if ($f->open($u) === true) {
            $f->extractTo(__BASE__.'../');
            $f->close();
            echo 'estrazione file completata! vai al processo di aggiornamento del database! se richiesto.';
        } else {
            echo 'estrazione file fallita!Applicazione corrotta?';
        }
    }
}
