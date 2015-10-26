<?php
require_once __BASE__.'/lib/mpdf/mpdf.php';
 
##
class HtmlPdf { 
	
	##
	private $pdf = null;
	
	##
	private $html = null;
	
        ##
        private $css = null;
        
	##
	private $header = null;
	
	##
	private $footer = null;
	
	##
	private $title = null;
     	
	##
	public function __construct($title,$css="") {
		$css=isset($css) && $css != ""?$css:__URL__."/public/css/pdf.css";	
		
                $this->title = $title;
		$this->css = file_get_contents($css);
                
		## init pdf handler
		$this->pdf = new mPDF('','A4','','',20,20,20,20,16);
		$this->pdf->SetAutoFont();                
		
		$this->pdf->SetTitle($this->title);
		$this->pdf->SetAuthor('NyxSoftware SRL');
		$this->pdf->SetCreator('ISB Gestionale');
		$this->pdf->SetHeader('||'.$this->title);    
		$this->pdf->SetFooter('||Pagina n. {PAGENO} di {nb}');
		$this->pdf->SetWatermarkImage(__LOGO__, 1, array(18,10), array(20,8));
        $this->pdf->showWatermarkImage = true;
        /*$this->pdf->SetWatermarkText('ISB '.$this->title);
        $this->pdf->showWatermarkText = true;*/
			
	}
	
	##
	public function output() {
			
		## call render function prepare HTML
		ob_start();
		$this->render();
		$this->html = ob_get_clean();
		
		## call render header
		ob_start();
		$this->header();
		$this->header = trim(ob_get_clean());
		
		if ($this->header) {
			$this->pdf->SetHTMLHeader($this->header);
		}
		
		## call render header
		ob_start();
		$this->footer();
		$this->footer = trim(ob_get_clean());		
		
		if ($this->footer) {
			$this->pdf->SetHTMLFooter($this->footer);
		}
		
                ## pass css to processor
                $this->pdf->WriteHTML($this->css,1);
		## pass html to processor
		$this->pdf->WriteHTML(utf8_encode($this->html),2);
				                
		## generate output
		$this->pdf->Output();
		
		##
		exit();
	}
	
	##
	public function header() {
		
	}
	
	##
	public function footer() {
		
	}
	
}
