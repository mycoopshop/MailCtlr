<?php

##
class GridColumn {
	
	private $alias = false;
	
	private $field;
	
	private $label;
	
	private $visible = true;
	
	private $defaultSort;
	
	private $sortable = true;
	
    private $searchable = true;
    
	private $cssWidth;
	
	private $html;
	
	private $function;
	
	public function __construct() {
		 
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function hasAlias() {
		return (boolean) $this->alias;		
	}
	
	public function setLabel($label) {
		$this->label = $label;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
    public function setSearchable($searchable) {
		$this->searchable = $searchable;
	}
	
	public function getSearchable() {
		return $this->searchable;
	}
    
	public function setField($field) {
		$this->field = $field;
	}
	
	public function getField() {
		return $this->field;
	}
		
	public function setVisible($visible) {
		$this->visible = $visible;
	}
	
	public function getVisible() {
		return $this->visible;
	}
	
	public function isVisible() {
		return $this->visible;
	}
	
	public function setDefaultSort($sort="asc") {
		$this->defaultSort = strtolower($sort);		
	}
	
	public function getDefaultSort() {
		return $this->defaultSort;		
	}
	
	public function isSortable() {
		return $this->sortable;
	}
	
	public function setSortable($allowSorting) {
		$this->sortable = $allowSorting;
	}
	
	public function setWidth($width) {
		$this->cssWidth = $width;		
	}
	
	public function getWidth() {
		return $this->cssWidth;		
	}
	
	public function setHtml($html) {
		$this->html = $html;		
	}
	
	public function getHtml() {
		return $this->html;		
	}
	
	public function setFunction($function) {
		$this->function = $function;		
	}
	
	public function getFunction() {
		return $this->function;		
	}
	
}
