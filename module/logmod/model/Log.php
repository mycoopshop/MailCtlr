<?php

require_once __BASE__.'/model/Storable.php';
##
class Log extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;
    public $action = '';//azione fatta
    public $dettaglio = MYSQL_TEXT;
    public $objcet = MYSQL_TEXT;
    public $data = MYSQL_DATETIME;
    //dati del pc client connesso
    public $user_id = 0;
    public $user_ip = '';
    public $user_hostname = '';
    public $user_city = '';
    public $user_region = '';
    public $user_country = '';
    public $user_loc = '';
    public $user_org = '';

    public static function Logga($action, $dettaglio = '', $objcet = '')
    {
        $app = App::getInstance();
        $info_user = User::getInfoUser();
        $log_info = [
            'action'        => mysql_real_escape_string($action), //azione fatta
            'dettaglio'     => mysql_real_escape_string($dettaglio),
            'objcet'        => mysql_real_escape_string($objcet),
            'data'          => MYSQL_NOW(),
            'user_id'       => $app->user['id'],
            'user_ip'       => $info_user->ip,
            'user_hostname' => $info_user->hostname,
            'user_city'     => $info_user->city,
            'user_region'   => $info_user->region,
            'user_country'  => $info_user->country,
            'user_loc'      => $info_user->loc,
            'user_org'      => $info_user->org,
        ];
        $log = self::submit($log_info);

        return $log->id;
    }
}

Log::schemadb_update();
