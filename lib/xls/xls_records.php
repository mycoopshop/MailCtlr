<?php
require_once __DIR__.'/php-excel-reader/excel_reader2.php';
require_once __DIR__.'/SpreadsheetReader.php';

function xls_records($xls) {
	
	##
	$xls = new SpreadsheetReader($xls);
	$out = array();
	$def = array();
   	
	##
	foreach($xls as $r => $row) {
		if ($r>1) {
			foreach($row as $c=>$val) {
				$out[$r-1][trim($col[$c])] = $val;				
			}			
		} else {
			$col = $row;
		}
    }
	
	##
	return $out;
}