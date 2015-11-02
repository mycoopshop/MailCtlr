<?php

## Liberty kernel class
class Liberty {
	
	## set debug 
	public static function debug($flag) {		
		error_reporting(E_ALL);
		ini_set('html_errors', $flag);
		ini_set('display_errors', $flag);
		ini_set('display_startup_errors', $flag);
		if (function_exists('xdebug_disable')) {
			xdebug_disable();
		}
	}
	
	## retrienve configuration array
	public static function config() {
		
		## filename of configuration array
		$f = __BASE__.'/config/'.__NAME__.'.'.__MODE__.'.php';
		
		## check and return
		if (file_exists($f)) {
			$c = require($f);
			$d = array(
				'controller' => 'Home',
				'action' => 'index',
			);
			foreach($d as $k=>$v) {
				$c['default'][$k] = isset($c['default'][$k]) ? $c['default'][$k] : $v;  
			}
			return $c; 
		} else {
			$m = "[Liberty 101] file not found: {$f}";
			$e = E_USER_ERROR;
			trigger_error($m,$e);
			exit();
		}
		
	}
	
	## parse request uri	
	public static function request_parse($request_uri,$php_self,$default_controller='Home',$default_action='index') {
		$a = parse_url($request_uri);
		$b = explode("/",substr($a["path"],1));
		$c = explode("/",substr($php_self,1));
		$d = array_slice($b,count($c)-1);
		$e = !in_array($d[0],array('','index.php',false,NULL)) ? ucfirst($d[0]) : $default_controller;
		$f = isset($d[1]) && !in_array($d[1],array("","index.php",false,NULL)) ? strtolower($d[1]) : $default_action;
		$g = array();if(count($d)>2){foreach(array_slice($d,2) as $i=>$v){if($i%2==0){$g[$v]=null;$h=$v;}else{$g[$h]=$v;}}}
		return array(
			'tokens'	=> $d,
			'name'		=> $e,
			'class'		=> $e.'Controller',
			'basename'	=> $e.'Controller.php',
			'action'	=> $f,
			'method'	=> $f.'Action',
			'params'	=> $g,
		);
	}
		
	## trigger error
	public static function trigger_error($msg) {
		trigger_error($msg,E_USER_ERROR);		
	}
	
	## 
	public static function testAcl($roles,$path,$acl) {
		
		if (!is_array($roles)) {
			$roles = array($roles);
		}
		
		$logs = "";
		$logs.= "acl: $path\n";
		
		$path = explode(".",$path);
		$exit = 0;
				
		foreach($roles as $role) {
			$role = $role ? $role : "public";
			if (isset($acl[$role])) {
				$logs.= "   - role($role)\n";
				foreach($acl[$role] as $rule => $allow) {
					$allow = (int)$allow;
					$logs.= "     rule($rule,$allow)\n";										
					$rule = explode(".",$rule);
					if (count($path)<count($rule)) {
						$sel0 = $path;
						$sel1 = $rule;						
					} else {
						$sel0 = $rule;						
						$sel1 = $path;						
					}
					$been = 1;
					for($i=0;$i<count($sel0);$i++) {
						if ($sel0[$i]=="*" || $sel1[$i]=="*") {
							$logs.= "         #$i [*] $sel0[$i] $sel1[$i] \n";															
							continue;
						} else if ($sel0[$i]==$sel1[$i]) {
							$logs.= "         #$i [=] $sel0[$i] $sel1[$i] \n";															
							continue;
						} else if ($sel0[$i]!=$sel1[$i]) {
							$logs.= "         #$i [.] $sel0[$i] $sel1[$i] \n";
							$been = 0;
							break;
						}						
					}
					$logs.= "          stop $been $allow\n";																													
					if ($been) {
						$exit = $allow;
					}					
				}
			}
		} 
		$logs.= "   - exit $exit\n";
				
        //echo '<pre>'.$logs.'</pre>';		
		//die();
				
		return $exit;
	}
	
}