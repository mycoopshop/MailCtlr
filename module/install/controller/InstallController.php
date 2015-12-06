<?php   
   
class InstallController {
	
    ##
    public function indexAction(){
        
        $app = App::getInstance();
        $app->render_view();
    }
    
    public function installAction(){
       
        if (empty($_POST)) die();
        
        $date = $_POST;

        ##iscrivi su ctlr
        if ($date['news'] == 1){
            $url = 'http://www.ctlr.it/mailctlr/remote/subscribe/';
            $fields = array(
                'email' => urlencode($_POST['email']),
                'nome' => urlencode($_POST['nome']),
                'cognome' => urlencode($_POST['cognome']),
                'privacy' => urlencode(1),
                'lista' => urlencode(1),

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

        global $db;
        $db = schemadb::connect(
            $opt['db']['host'],
            $opt['db']['user'],
            $opt['db']['pass'],
            $opt['db']['name'],
            $opt['db']['pref']
        );   

        require_once __BASE__.'/module/userrole/model/User.php';
        $id = User::submit(array(
            'username' => mysql_real_escape_string( $date['u_username'] ),
            'password' => md5( mysql_real_escape_string($date['u_password']) ),
            'nome' => mysql_real_escape_string( $date['nome']),
            'cognome' => mysql_real_escape_string( $date['cognome']),
            'email' => mysql_real_escape_string( $date['email']),
            'role' => 'superadmin',
            'lastedit' => MYSQL_NOW(),
        ));

        $optdb = array(
            'debug' => 'false',
            'version' => '0.1.2',
            'url' => "http://" . $_SERVER['HTTP_HOST'] . $date['folder'],//;str_replace("install/","",__URL__),
            'home' => "http://" . $_SERVER['HTTP_HOST'] . $date['folder'],//str_replace("install/","",__HOME__),
            'logo' => '/store/mailctlr/logo-mailctlr.png',
            'default' => serialize(array('theme' => 'default','controller' => 'Dashboard','action'=>'index')),
            'modules' => serialize(array('dashboard','contact','sender','config','userrole','changelog',)),
            'total_send' => 0,
            'mail' => $id->email,
        );
        require_once __BASE__.'/module/config/model/Options.php';
        foreach($optdb as $key => $value) { 
            Options::submit(array(
                'name' => $key,
                'value' => $value,
                'type' => $date['type'],
                'last_edit' => MYSQL_NOW(),
            ));
        }
        $date['nome_app'] = strtolower($date['nome_app']);
        
        $conf_name = __DIR__."/../../../config/{$date[nome_app]}.{$date[type]}.php";
        $config = "<?php "."\r\n"
                . "define('DAY',1);"."\r\n"
                . "define('WEEK',7);"."\r\n"
                . "define('MONTH',30);"."\r\n"
                . "define('YEAR',365);"."\r\n"
                . "return ".var_export($opt, true).";";
        
        $index_file = __DIR__."/../../../index.php";
        $index = "<?php "."\r\n"
                . " define('__NAME__','{$date['nome_app']}');"."\r\n"
                . " define('__MODE__','{$date['type']}');"."\r\n"
                . " require_once 'bootstrap.php';"."\r\n"
                . " require_once __BASE__.'/app/mailctlr/MailCtlrWebApp.php';"."\r\n"
                . " \$app = new MailCtlrWebApp( __FILE__ , ".chr('36')."_SERVER['PHP_SELF'], ".chr('36')."_SERVER['REQUEST_URI'] ); "."\r\n"
                . " \$app->run(); ";
        
        $htaccess_file = __DIR__."/../../../.htaccess";
        $htaccess = "<IfModule mod_rewrite.c>"."\r\n"
                    . "RewriteEngine On"."\r\n"
                    . "RewriteBase ".$date['folder']."\r\n"
                    . "RewriteRule ^index\.php$ - [L]"."\r\n"
                    . "RewriteCond %{REQUEST_FILENAME} !-f"."\r\n"
                    . "RewriteCond %{REQUEST_FILENAME} !-d"."\r\n"
                    . "RewriteRule . ".$date['folder']."index.php [L]"."\r\n"
                    . "</IfModule>";
        
        file_put_contents($conf_name, $config );
        //echo "FILE: {$conf_name} creato!<br />";
        file_put_contents($index_file, $index);
        //echo "FILE: {$index_file} creato!<br />";
        file_put_contents($htaccess_file, $htaccess);
        //echo "FILE: {$htaccess_file} creato!<br />";
        
        $app = App::getInstance();
        $app->redirect("http://" . $_SERVER['HTTP_HOST'] . $date['folder']);
    }
       
}
