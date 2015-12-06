<?php

require_once __BASE__.'/model/Storable.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Contact.php';

##
class Coda extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;

    public $user_id = 0;
    public $contact_id = 0;
    public $email_id = 0;

    public $created = MYSQL_DATETIME;
    public $execute = MYSQL_DATETIME;
    public $server_id = 0;
    public $processato = 0;
    public $note = MYSQL_TEXT;

    public $last_see = MYSQL_DATETIME;

    ##

    public static function addCode($lista = '', $email = '')
    {
        $app = App::getInstance();

        $is = Iscrizioni::getList($lista);
        foreach ($is as $iscrizione) {
            $c = Contact::load($iscrizione->contatto_id);
            if (!$c->active || !isset($c->id)) {
                $iscrizione::delete($iscrizione->id);
            }
            self::submit([
                'contact_id'    => $iscrizione->contatto_id,
                'created'       => MYSQL_NOW(),
                'email_id'      => $email,
                'user_id'       => $app->user['id'],
            ]);
        }
    }

    ##

    public static function attendSend()
    {
        return self::count('verify0');
    }

    ##

    public static function count($type = '')
    {
        $append = ' ';
        switch ($type) {
            case 'verify0':
                $append .= " WHERE processato = '0' ";
                break;
            case 'verify1':
                $append .= " WHERE processato = '1' ";
                break;
        }

        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table().$append;
        $res = schemadb::execute('row', $sql);

        return $res['totale'];
    }
}
Coda::schemadb_update();
