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
     * Check current version from release server
    */
    ##
    
    public function checkVersionAction()
    {
        $json = file_get_contents('http://www.mailctlr.org/current/info.php');
        $data = json_decode($json);
        if (__VERSION__!=$data->version_numb){
            $cl = new Changelog();
            $cl->store($data);
            $data->update = 1;
        }
        return $data;
    }
    
    ##
    
    public function updateAction($v = "latest")
    {
        $u = __BASE__."/$v.zip";
        file_put_contents($u, fopen("http://www.mailctlr.org/current/latest.zip", 'r'));
        $f = new ZipArchive;
        if ($f->open($u) === TRUE) {
            $f->extractTo(__BASE__."../");
            $f->close();
            echo "estrazione file completata! vai al processo di aggiornamento del database! se richiesto.";
        } else {
            echo "estrazione file fallita!Applicazione corrotta?";
        }
        
    }
    
    
}
