<?php

require_once __BASE__.'/model/Storable.php';
class AccountSMTP extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;

    public $code = '';
    public $created = MYSQL_DATETIME;
    public $last_edit = MYSQL_DATETIME;

    public $name = '';

    public $host = '';
    public $port = '';
    public $connection = '';
    public $username = '';
    public $password = '';
    public $sender_name = '';
    public $sender_mail = '';

    public $replyTo = '';

    public $max_mail = 0;
    public $ever = ['day', 'week', 'month', 'year', 'onetime'];
    public $max_mail_day = 0;

    public $send = 0;
    public $last_send = MYSQL_DATETIME;
    public $perc = 0.0;

    public $total_send = 0;

    public $active = 1;

    ##

    public static function findServer()
    {
        $servers = self::query(
                [
                    'active'  => 1,
                ]);
        $perc = 110;
        foreach ($servers as $server) {
            if ($server->perc < $perc) { //trova quello più saturo
                $use = $server;
            }
            if ($server->perc == 0) {
                return $use;
            }
        }

        return $use;
    }

    ##

    public static function getMaxMail($account = 'all')
    {
        $append = $account != 'all' ? ' WHERE id = "'.$account.'"' : ' ';
        $sql = 'SELECT SUM(max_mail_day) AS maxmail FROM '.self::table().$append;
        $res = schemadb::execute('row', $sql);

        return $res['maxmail'];
    }

    ##

    public static function getSenderMail($account = 'all')
    {
        $append = $account != 'all' ? ' WHERE id = "'.$account.'"' : ' ';
        $sql = 'SELECT SUM(total_send) AS mailtotali FROM '.self::table().$append;
        $res = schemadb::execute('row', $sql);

        return $res['mailtotali'];
    }

    ##

    public static function getInviateMail($account = 'all')
    {
        $append = $account != 'all' ? ' WHERE id = "'.$account.'"' : ' ';
        $sql = 'SELECT SUM(send) AS inviate FROM '.self::table().$append;
        $res = schemadb::execute('row', $sql);

        return $res['inviate'];
    }

    ##

    public static function getRemainMail($account = 'all')
    {
        $remain = self::getMaxMail() - self::getInviateMail();

        return $remain;
    }
}
AccountSMTP::schemadb_update();
