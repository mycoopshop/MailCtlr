<?php
define('_SPDF_VERSION_','0.1.1');

## Please include the FPDF library (http://www.fpdf.org/)
if (!class_exists("FPDF")) {	
	#trigger_error("Class not found FPDF, please include FPDF library", E_USER_WARNING);
	include_once(__DIR__.'/lib/fpdf17/fpdf.php');
}

## SPDF main class
class SPDF {
	
	##
	private $handle = NULL;	
	
	##
	private $engine = NULL;
	
	##
	private $debug = false;
	
	##
	private $primaryX = 0;
	
	##
	private $primaryY = 0;
	
	##
	private $currentX = 0;
	
	##
	private $currentY = 0;
	
	##
	public function __construct() {
		$this->engine = new SPDF_STYLE_ENGINE();
		$this->engine->addStyle(__DIR__.'/spdf.css');
		$this->handle = new FPDF();
		$this->page();
		$this->primaryX = $this->handle->GetX();
		$this->primaryY = $this->handle->GetY();
	}
	
	##
	public function setHandle( &$pdf ) {
		$this->handle = &$pdf;		
	}
	
	public function addStyle($filename) {
		$this->engine->addStyle($filename);
	}
	
	public function move($x,$y) {
		$this->handle->SetXY($x,$y);
		return $this;
	}
	
	##
	public function text( $text , $style='' ) {
		
		$h = &$this->handle;				
		$e = &$this->engine;
		$s = "__pdf__ element text default {$style}";
		
		$p = $e->getProperties($s,array(
			'display'		=> 'inline',
			'font-family'	=> 'Arial',
			'font-style'	=> "",
			'font-size'		=> 15,
			'letter-width'	=> 'auto',
			'line-height'	=> 10,
		));	
		
		##
		if ($this->debug) {
			$this->dump($p);
		}
		
		## 
		$h->SetFont(
			$p['font-family'],
			SPDF::SetFontStyle($p['font-style']),
			SPDF::SetFontSize($p['font-size'])
		);
		
		if ($p['display']=='block') {
			$h->Cell(0,$p['line-height'],$text,'',1);
			
		} else {
			if ($p['letter-width']>0) {
				for($i=0;$i<strlen($text);$i++) {
					$h->Cell(10,10,$text[$i]);			
				}
			} else {
				$h->Cell(10,$p['line-height'],$text);			
			}
			
		}

		##
		$this->setCurrentXY();
		
		##
		return $this;
	}	
	
	public function line($x1,$y1,$x2,$y2, $style="" ) {
		
		$h = &$this->handle;
		$s = $this->_parse_style("__pdf__ element line default ".$style);
		
		$line_color = $this->_parse_color($this->_get($s,"color"));
		$line_width = $this->_get($s,"line-width");
		
		$h->SetLineWidth($line_width);
		$h->SetDrawColor($line_color[0],$line_color[1],$line_color[2]);
		
		$h->Line($x1,$y1,$x2,$y2);
		
		return $this;
	} 
	
	public function rect($x1,$y1,$x2,$y2, $style="" ) {
		
		$h = &$this->handle;
		$s = $this->_parse_style("__pdf__ element rect default ".$style);
		
		$line_color = $this->_parse_color($this->_get($s,"color"));
		$line_width = $this->_get($s,"line-width");		
		$h->SetLineWidth($line_width);
		$h->SetDrawColor($line_color[0],$line_color[1],$line_color[2]);
		
		$background_color = $this->_get($s,"background","transparent");		
		$background_color = $this->_parse_color($background_color);		
		$h->SetFillColor($background_color[0],$background_color[1],$background_color[2]);		
		
		$rect_style = $background_color != "transparent" ? "FD" : "D";		
		$h->Rect($x1,$y1,$x2,$y2,$rect_style);
		
		return $this;
	} 
	
	public function grid() {
		$c = 10;
		$h = &$this->handle;		
		$h->SetLineWidth(0.1);
		$h->SetDrawColor(200,200,200);
		$h->SetFont("Arial","",5);
		for ($x=0;$x<21;$x++) {
			for ($y=0;$y<27;$y++) {
				$h->SetXY($x*$c,$y*$c);
				if ($y==0 || $x==0) {
					$label = ($x*$c).",".($y*$c);
				} else {
					$label = "";
				}
				$h->Cell($c,$c,$label,1,"","C");
			}
		}		
		
		return $this;
	}
	
	##
	public function table($table,$style='') {
		
		##
		$h = &$this->handle;		
		$e = &$this->engine;		
		$s = "__pdf__ element table default {$style}";
		
		##
		$p = $e->getProperties($s,array(			
			'font-family'	=> 'Arial',
			'font-style'	=> "",
			'font-size'		=> 12,
			'line-height'	=> 10,
		));	
		
		## 
		$h->SetFont(
			$p['font-family'],
			SPDF::SetFontStyle($p['font-style']),
			SPDF::SetFontSize($p['font-size'])
		);
		
		##
		$x0 = $this->primaryX; 
		$y0 = $this->currentY;
		
		$rowCount = 0;
		foreach($table as $row) {
			$colCount = 0;
			foreach($row as $cell) {				
				$w = 50;		
				$l = 10;
				$x = $x0 + $colCount*$w;
				$y = $y0 + $rowCount*$l;
				$h->setXY($x,$y);		
				$h->Cell($w,$l,$cell);
				$colCount++;
			}		
			$rowCount++;			
		}
	} 
	
	public function page($selector="") {
		
		$h = &$this->handle;
		$e = &$this->engine;
		$s = "__pdf__ element page default $selector";
				
		$p = $e->getProperties($s,array(
			
		));
		
		$h->AddPage();				
		
		/*
		## draw background of page
		$background_color = $this->_get($s,"background","transparent");		
		$background_color = $this->_parse_color($background_color);		
		$h->SetFillColor($background_color[0],$background_color[1],$background_color[2]);		
		
		$h->Rect(0,0,210,300,"F");
		$h->SetXY(0,0);		
		*/
		
		return $this;
	}
	
	##
	public function show() {
		$this->handle->Output();
		die();
	}	

	
	##
	private static function SetFontStyle($style,$weigth='') {
		
		if (!$style) {
			return '';
		}
		
		switch($style) {
			case 'italic': return 'i';			
		}
		
		die("Undefined font-style:'{$style}'");
	}
	
	##
	private static function SetFontSize($size) {
		return $size;		
	}

	##
	private function setCurrentXY() {
		$this->currentX = $this->handle->GetX();
		$this->currentY = $this->handle->GetY();		
	}
	
	##
	public function style($style,$properties) {
		$this->engine->setStyle($style,$properties);
	}
	
	##
	public function dump($var) {
		$h = &$this->handle;
		$d = var_export($var,true); 
		
		$y = $h->GetY() + 10;
		
		$h->SetFont("Courier","",10); 
		
		$h->SetX(0);
		$h->SetY($y);
		$h->MultiCell(200, 4, $d);	
	}
	
	##
	public function debug($flag) {
		$this->debug = $flag;
	}
	
	##
	public function dumpStyles() {
		$this->engine->dumpStyles();
	}
}

##
class SPDF_STYLE_ENGINE {
	
	##
	private $styles = array();
	
	##
	public function addStyle( $sheet ) {
		$source = file_get_contents($sheet);
	 	    
		preg_match_all("/([^\{]*)\{([^\{]*)\}/i",$source,$styles);		
		foreach($styles[0] as $i=>$s) {
			$style = strtolower(trim($styles[1][$i]));			
			$array = array();
			preg_match_all("/([^\:]*)\:([^\:]*)\;/i",$styles[2][$i],$attr);
			foreach($attr[0] as $j=>$a) {
				$k = strtolower(trim($attr[1][$j]));
				$v = strtolower(trim($attr[2][$j]));
				$array[$k] = $v;
			}
			$this->setStyle($style,$array);
		}	
		
	}

	public function setStyle( $style , $properties ) {
		if (isset($this->styles[$style])) {
			foreach($properties as $k=>$v) {
				$this->styles[$style][$k] = $v;
			}
		} else {
			$this->styles[$style] = $properties;
		}		
	}
	
	
	function _parse_style($style) {
		if (isset($this->cached["styles"][$style])) {
			return $this->cached["styles"][$style];
		} else {
			$this->cached["styles"][$style] = explode(" ",$style);			
			return $this->cached["styles"][$style];
		}		
	}

	function _parse_color($color) {
		$color = trim($color);
		if ($color[0]=="#") {
			if (strlen($color)<5) {
				return array(
					hexdec(substr($color,1,1)),
					hexdec(substr($color,2,1)),
					hexdec(substr($color,3,1)),
				);
			} else {
				return array(
					hexdec(substr($color,1,2)),
					hexdec(substr($color,3,2)),
					hexdec(substr($color,5,2)),
				);
			}		
		}		
	}
	
	public function getProperties($selector,$default=array()) {
		$styles = explode(" ",$selector);
		$return = $default;
		$return['selector'] = $selector;
		
		foreach($styles as $style) {
			if (isset($this->styles[$style])) {
				foreach($this->styles[$style] as $k => $v) {
					$return[$k] = $v;
				}
			}
		}
		
		return $return;
	}
	
	
	public function dumpStyles() {
		echo '<pre>';
		var_dump($this->styles);
		echo '</pre>';
	}
	
}