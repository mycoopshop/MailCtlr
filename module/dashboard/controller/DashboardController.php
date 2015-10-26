<?php

##
class DashboardController {
	
	##
	public function indexAction() {
		
		##
		$app = App::getInstance();
		
		
		##
		$app->render();
	}
        public function testAction(){
               $app=App::getInstance();
               $app->render();
        }
		
}