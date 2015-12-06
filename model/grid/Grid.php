<?php
require_once __BASE__.'/model/grid/GridSql.php';
require_once __BASE__.'/model/grid/GridTable.php';
require_once __BASE__.'/model/grid/GridColumn.php';
require_once __BASE__.'/model/grid/GridService.php';
require_once __BASE__.'/model/grid/GridComposer.php';

class Grid {
	
	## customizable params
	public $id		= null;
	public $source		= null;	
	public $endpoint	= null;
	public $columns		= null;
	public $events		= null;

	## return grid handleder 
	public function getGridTable(){
			
		## init grid info for control datalist
		$grid = new GridTable();
		
		## set action will handle the grid fill
		$grid->setService($this->endpoint);
				
		## compose columns into $grid
		GridComposer::columns($grid, $this->columns);
		
		## compose events into $grid
		GridComposer::events($grid, $this->events);
		
		## return
		return $grid;
		
	}
	
	## generate html output to show
	public function html() {
		ob_start();
		
		$grid = $this->getGridTable();
					
		include __THEME__.'/grid.phtml';
		
		$html = ob_get_clean();		
		return $html;
	}
	
	##
	public function getClientInputs() {
		
		
		return (array) json_decode($_POST['gridTable']);
		
	}
	
	## generate api service response to intercat with view
	public function json() {
										        
		$grid = $this->getGridTable();
		
		$fields = array();
		$templates = array();
		$functions = array();
		
		foreach($grid->getColumns() as $column) {
			if ($column->hasAlias()) {
				$fields[$column->getAlias()] = $column->getField();
				$templates[$column->getAlias()] = $column->getHtml();
				$functions[$column->getAlias()] = $column->getFunction();
			} else {	
				$fields[] = $column->getField();
				$templates[$column->getField()] = $column->getHtml();
				$functions[$column->getField()] = $column->getFunction();
			}
		}
		
		$filter['fields'] = $fields;
		$filter['templates'] = $templates;
		$filter['functions'] = $functions;
		
		return GridService::getGridServiceResponse($this->source,$filter);				
	}
       
}