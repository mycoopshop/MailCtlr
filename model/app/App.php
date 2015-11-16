<?php
##
require_once(__BASE__.'/lib/curi/curi.php');
require_once(__BASE__.'/lib/idate/idate.php');

##
class App {
	
	##
	public $name	= "App";
	
	##
	public $hooks	= array();	
	
	##
	public $default = array();
	
	## static app singletone
	private static $instance = NULL;
	
	## 
	public function __construct($file,$php_self,$request_uri) {		
		global $config;
		
		##
		App::$instance = &$this; 
		
		$this->name = $config['name'];		
		$this->file = $file;
				
		$this->request	= Liberty::request_parse(
			$request_uri,
			$php_self,
			$config['default']['controller'],
			$config['default']['action']
		);				
	}
	
	##
	public static function &getInstance() {
		return static::$instance;
	}	
	
	## Load controller
	public function load() {
		
		##
		$this->load_modules();
		
		##
		if (!isset($this->module)) {
			$msg = "[Liberty 202] no module found";
			$err = E_USER_ERROR;
			$this->log($msg,$err);
			$this->error($msg,$err);
			exit();
		}
		
		##
		$this->load_controller();
		
		##
		if (!$this->request['file_exists']) {			
			$msg = "[Liberty 201] controller file '{$this->request['basename']}' not found in module/s: ".implode(', ',array_keys($this->module));
			$err = E_USER_ERROR;
			$this->log($msg,$err);
			$this->error($msg,$err);
			exit();
		}
			
		##
		if (!class_exists($this->request['class'])) {
			$msg = "controller class '{$this->request['class']}' not found in file: {$this->request['file']}";
			$err = E_USER_ERROR;			
			$this->log($msg,$err);
			$this->error($msg,$err);
			exit();
		}

		## controlla se il methodo esiste nel modulo
		if (!method_exists($this->request['class'],$this->request['method'])) {
			$msg = "method action '{$this->request['method']}' not found in class '{$this->request['class']}'";
			$err = E_USER_ERROR;
			$this->log($msg,$err);
			$this->error($msg,$err);
			exit();
		}
					
		## prepare acl query
		## test ACL permission for this module/controller/action
		$this->request['acl'] = 
        $this->request['module'].'.'. 
        $this->request['name'].'.'. 
        $this->request['action'];		
	}
	
	
	public function load_modules() {
		global $config;
		
		## remove duplicates
		$config['modules'] = array_unique($config['modules']);
		
		##
		if (count($config['modules'])>0) {
			foreach($config['modules'] as $m) {
				$p = __BASE__.'/module/'.$m;
				$f = $p.'/'.$m.'.php';
				if (file_exists($f)) {
					require_once($f);
					$c = ucfirst($m).'Module';
					$o = new $c();
					$this->module[$m] = array(
						'name'		=> $m,						
						'path'		=> $p,
						'class'		=> $c,
						'object'	=> $o,						
					);
				} 
			}								
		}
	}
	
	public function load_controller() {
		
		$this->request['file_exists'] = false;
		
		if (isset($this->module)) {
			foreach($this->module as $module) {			
				$controller_file =  $module['path'].'/controller/'.$this->request['basename'];								
				if (file_exists($controller_file)) {
					$this->request['path'] = __BASE__.'/module/'.$module['name'];
					$this->request['file'] = $controller_file;
					$this->request['file_exists'] = true;
					$this->request['module'] = $module['name'];					
					require_once($controller_file);
					break;
				} 			
			}				
		}
	}
	
	
	## dispatch output
	public function exec() {										
		## instanzia il controller
		## con implicata chiamata al costruttore
		$controller_class	= $this->request['class'];
		$controller_object	= new $controller_class();

		## chiama il metodo del controller
		call_user_func(array($controller_object,$this->request['method']));
	}
	
	
	public function run() {
		$this->load();
		$this->exec();
	}
		
	## Handle Errors
	public function error($msg,$err) {		
		trigger_error($msg,$err);
	}

	## Handle Logs
	public function log($error) {
		#	echo 'error!';
		#debug_print_backtrace();		
	}
	
	
	## 
	public function redirect($url) {
		header('Location: '.$url);
		exit();
	}
}

