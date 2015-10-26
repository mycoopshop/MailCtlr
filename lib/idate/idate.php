<?php

##
class idate {
	
	##
	public static function convert($date,$from,$to) {
		
		##
		$token = array();
		
		## parse input
		switch($from) {
			##
			case 'it':
				$temp = explode('/',$date);
				$token['Y'] = (int)@$temp[2];  
				$token['m'] = (int)@$temp[1];  
				$token['d'] = (int)@$temp[0];  
				break;
			
			##
			case 'mysql':
				$temp = explode('-',$date);
				$token['Y'] = (int)@$temp[0];  
				$token['m'] = (int)@$temp[1];  
				$token['d'] = (int)@$temp[2];  
				break;
			
		}
		
		## build output
		switch($to) {
			
			##
			case 'mysql': 
				return $token['Y'].'-'.$token['m'].'-'.$token['d'];				
		
			case 'it':
				return $token['d'].'/'.$token['m'].'/'.$token['Y'];
				
			##
			default:
				return $date;
		}				
	}
	
}