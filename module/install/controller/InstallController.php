<?php   

//require_once __BASE__.'/module/config/model/Options.php';

class InstallController {
	
    ##
    public function indexAction(){
        $app = App::getInstance();
        $app->render_view();
    }
    
    public function installAction(){
        $date = $_POST;

        ##iscrivi su ctlr
        if ($date['news'] == 1){
            $url = 'http://www.ctlr.eu/MailCtlr/remote/subscribe/';
            $fields = array(
                'email' => urlencode($_POST['email']),
                'nome' => urlencode($_POST['nome']),
                'cognome' => urlencode($_POST['cognome']),
                'privacy' => urlencode(1),
                'lista' => urlencode(2),

            );
            $fields_string = "";
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            $result = curl_exec($ch);
            curl_close($ch);        
        }
        //
        /*
        $id = User::submit(array(
            'username' => mysql_real_escape_string( $date['u_username'] ),
            'password' => md5( mysql_real_escape_string($date['u_password']) ),
            'nome' => mysql_real_escape_string( $date['nome']),
            'cognome' => mysql_real_escape_string( $date['cognome']),
            'email' => mysql_real_escape_string( $date['email']),
            'role' => 'superadmin',
            'lastedit' => MYSQL_NOW(),
        ));
         */
        
        //$opt = $date; //array();
        
        $opt['name'] = $date['nome_app'];        
        $opt['db'] = array(
            'host' => $date['host'],
            'user' => $date['user'],
            'pass' => $date['pass'],
            'name' => $date['name'],
            'pref' => $date['pref'],
        );
        $opt['type'] = $date['type'];
        $opt['install'] = 1;
        /*
        global $db;
        $db = schemadb::connect(
            $opt['db']['host'],
            $opt['db']['user'],
            $opt['db']['pass'],
            $opt['db']['name'],
            $opt['db']['pref']
        );       
        */

        $optdb = array(
            'debug' => 'false',
            'version' => '0.1.2',
            'url' => __URL__,
            'home' => __HOME__,
            'logo' => '/store/mailctlr/logo-mailctlr.png',
            'default' => serialize(array('theme' => 'default','controller' => 'Dashboard','action'=>'index')),
            'modules' => serialize(array('dashboard','contact','sender','config','userrole','logmod','changelog',)),
        );
        
        foreach($optdb as $key => $value) { 
            Options::submit(array(
                'name' => $key,
                'value' => $value,
                'type' => $date['type'],
                'last_edit' => MYSQL_NOW(),
            ));
        }
        $conf_name = __DIR__."/../../../config/mailctlr.".$date['type'].".php";
        
        //$conf_file = fopen($conf_name, "w");
        
        $config = "<?php "."\r\n"."\r\n"
                . "define('DAY',1);"."\r\n"
                . "define('WEEK',7);"."\r\n"
                . "define('MONTH',30);"."\r\n"
                . "define('YEAR',365);"."\r\n"."\r\n"
                . "return ".var_export($opt, true).";"."\r\n";
        
        file_put_contents($conf_name, $config );
        echo "FILE: {$conf_name} creato!";
    }
    
    
    
}
