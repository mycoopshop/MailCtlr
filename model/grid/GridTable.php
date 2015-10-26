<?php

##
class GridTable {
	 	
	private $columns = array();
	
	private $service;
		
	function __construct() {

	}
	
	public function addColumn($column) {
		$this->columns[] = $column;
	}
	
	public function getColumns() {
		return $this->columns;
	}
	
	##
	public function setService($service) {
		$this->service = $service;
	}
	
	##
	public function getService() {
		return $this->service;
	}
		
	##
	public function getDefaultSortOrder() {
		$o = array();
		foreach($this->getColumns() as $i=>$c) {
			$s = $c->getDefaultSort();
			if ($s) {
				$o[] = array($i,$s);
			}
		}
		return json_encode($o);		
	}
	
	##
	public function getColumnsDefinition() {
		$d = array();
		foreach($this->getColumns() as $i=>$c) {
			$d[] = array(
				"bVisible"	=> $c->isVisible(),
				"bSortable" => $c->isSortable(),
				"sWidth"	=> $c->getWidth(),
			);			
		}		
		return json_encode($d);
	}
	
}
