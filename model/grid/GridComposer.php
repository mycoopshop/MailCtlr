<?php

##
class GridComposer {
	  
	## 
	public static function columns(&$grid,$columns) {
		
		##
		foreach($columns as $k=>$v) {
			
			$html = false;
			$alias = false;
			$field = $k;
			$visible = true;
			$sortable = true;
            $searchable = true;
			$width = 0;
			$function = false;
			
			if (is_array($v)) {
				if (isset($v['field'])) {
					$alias = $k;
					$field = $v['field'];
				} else if (isset($v['alias'])) {
					$alias = $v['alias'];
				}
				
				$html		= isset($v['html']) ? $v['html'] : false;			
				$label		= isset($v['label']) ? $v['label'] : $field;
				$width		= isset($v['width']) ? (int) $v['width'] : 0;
				$sortable	= isset($v['sortable']) ? (boolean) $v['sortable'] : true;
                $searchable	= isset($v['searchable']) ? (boolean) $v['searchable'] : true;
				$visible	= isset($v['visible']) ? (boolean) $v['visible'] : true;
				$function	= isset($v['function']) ? $v['function'] : false;
 
			} else {
				$field = $k;
				$label = $v;
			}
			
			$column = new GridColumn();
			$column->setHtml($html);
			$column->setAlias($alias);
			$column->setField($field);
			$column->setLabel($label);
			$column->setWidth($width);
            $column->setSortable($sortable);
            $column->setSearchable($searchable);            
			$column->setVisible($visible);
			$column->setFunction($function);
			//$column->setReadOnly(false);		
			$grid->addColumn($column);
			
		}
						
	}
	
	##
	public static function events(&$grid,$events) {
		foreach($events as $e=>$h) {
			$grid->ClientEvents[$e] = $h;
		}
	}
	
}