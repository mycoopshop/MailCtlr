<?php

require_once __BASE__.'/module/changelog/model/Changelog.php';

class ChangelogModule
{
    ##

    public function __construct()
    {
        /*
         * Check update
        */
        self::checkVersion();
    }

    /*
     * Check current version from release server
     *      don't work at now!!
    */
    ##

    public static function checkVersion()
    {
        $json = file_get_contents('http://www.mailctlr.org/current/info.php');
        $data = json_decode($json);
        if (__VERSION__ != $data->version_numb) {
            $data->updated = 1;
            $a = get_object_vars($data);
            $cl = Changelog::submit($a);
        }

        return $data;
    }
}
