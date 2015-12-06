<?php

require_once __BASE__.'/model/Storable.php';

class Options extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;

    public $name = '';
    public $descrizione = '';
    public $value = '';

    public $type = ['dev', 'alpha', 'beta', 'stable'];

    public $last_edit = MYSQL_DATETIME;

    public static function getOptions($type = 'stable')
    {
        $opts = self::query(['type' => $type]);
        foreach ($opts as $opt) {
            $s = @unserialize($opt->value);
            if ($s !== false) {
                $opt_p[$opt->name] = $s;
            } else {
                $opt_p[$opt->name] = $opt->value;
            }
        }

        return $opt_p;
    }
}
Options::schemadb_update();
