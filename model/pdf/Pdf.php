<?php
require_once __BASE__.'/lib/spdf/spdf.php';

##
class Pdf {
	
	##
	public function output() {

		## init pdf handler
		$this->pdf = new SPDF();
		
		## call render function
		$this->render();
		
		## generate output
		$this->pdf->show();
		
		##
		exit();
	}
	
	
}