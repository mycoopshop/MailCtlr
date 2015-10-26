<?php
require_once __BASE__.'/module/userrole/model/User.php';
class LoginController {	
	function indexAction() {		
		$app = App::getInstance();										
		$app->render_view();
	}
	function loginAction() {
		$app = App::getInstance();		
		$username = $_POST["username"];
		$password = $_POST["password"];
		$redirect = $_POST["redirect"];
		$remember = @$_POST["remember"];		
		if (User::canUserLogin($username,$password)) {			
			$user = User::fetchByLogin($username);	
			$app->setSessionUser($user->id,$user->username,array($user->role),$user->nome." ".$user->cognome);						
			if ($redirect) {
				$app->Redirect($redirect);
			} else {
				$app->Redirect(__HOME__);
			}			
		} else {
			$app->Redirect(__HOME__.'/login',array(
				"alert" => "Username non valido"				
			));			
		}		
	}
	//logout fix
	function logoutAction() {
		$app=App::getInstance();
		$app->setSessionUser('-1', 'undefined',array('public'));
		$app->Redirect(__HOME__.'/login');
	}		
}