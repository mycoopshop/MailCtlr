<?php
require_once __BASE__.'/lib/mimetype/MimeType.php';
require_once __BASE__.'/model/Storable.php';

##
class Files extends Storable {
	
	public $id = MYSQL_PRIMARY_KEY;
	public $folder = "";
	public $parent = 0;
	public $name = "";
	public $session = "";

	##
	public $path = "{base}/{folder}/{parent}/{name}";
		
	##
	public function response() {
		##
		$id	= isset($_GET['id']) ? $_GET['id'] : 0;
		$action = @$_GET['action'];
		$folder = @$_GET['folder'];
		$parent = (int) @$_GET['parent'];
		$session = @$_GET['session'];
		##
		switch($action) {
			
			case 'upload':
				$this->response_upload($folder,$parent,$session);
				break;
			
			case 'list':
				$this->response_list($folder,$parent);
				break;
			
			case 'delete':				
				$this->response_delete($id);
				break;
					
			case 'session':
				$this->response_session();
				break;
			
			case 'download':
				$this->response_download($id);
				break;
			
			case 'view':
				$this->response_view($id);
				break;
			
		}
			
	}
	
	## 
	public function response_upload($folder,$parent,$session) {
		
		$info = array_merge($_FILES['Filedata'],pathinfo($_FILES['Filedata']["name"]));								
		$name = $info['name'];
		$temp = $info['tmp_name'];
		
		$item = static::insert(array(
			'folder'	=> $folder,
			'parent'	=> $parent,
			//'file'		=> $file,	
			'name'		=> $name,
			'session'	=> $session,
		));
		
		$path = $item->getPath();
		
		$base = dirname($path);
				
		if (!is_dir($base)) {
			mkdir($base,0777,true);
		}
		
		move_uploaded_file($temp,$path);
	}
	
	##
	public function response_list($folder,$parent) {
		
		$all = static::query(array(
			'folder' => $folder,
			'parent' => $parent,
		));
		
		?>
		<ul class="list-group">
			<?php foreach($all as $row) { ?>
				<li class="list-group-item">
					<?=$row->name?>
					<button 
						data-ui-uploadify-view="<?=$row->id?>" 						
						class="btn btn-xs btn-primary"
						>
						&nbsp;<i class="glyphicon glyphicon-file"></i>
						Visualizza&nbsp;
					</button>
					<button 
						data-ui-uploadify-download="<?=$row->id?>" 						
						class="btn btn-xs btn-success"
						>
						&nbsp;<i class="glyphicon glyphicon-download"></i>
						Scarica&nbsp;
					</button>
					<button 
						data-ui-uploadify-delete="<?=$row->id?>" 
						class="btn btn-xs btn-danger"
						>
						&nbsp;<i class="glyphicon glyphicon-remove"></i>&nbsp;
					</button>
				</li>
			<?php } ?>	
		</ul>
		<?php				
	}
	
	##
	public function response_delete($id) {		
		static::delete($id);
	}

	##
	public function response_download($id) {		
		
		$file = static::load($id);
		$name = $file->name;
		$path = $file->getPath();
		$type = isset($file->type) && $file->type ? $file->type : MimeType::mime($name); 
		
		if (file_exists($path)) {
			header("Content-type: ".$type);
			header("Pragma: public");
			header("Cache-Control: private");
			header("Content-Disposition: attachment; filename=$name");
			header("Content-Description: PHP Generated Data");
			header('Content-Length: '.filesize($path));
			readfile($path);	
		} else {
			echo '<h1>File not found</h1>';
		}
				
		exit();
	}

	##
	public function response_view($id) {		
		
		$file = static::load($id);
		$name = $file->name;
		$path = $file->getPath();
		$type = isset($file->type) && $file->type ? $file->type : MimeType::mime($name); 
		
		if (file_exists($path)) {
			header("Content-type: ".$type);
			header("Pragma: public");
			header("Cache-Control: private");
			header("Content-Disposition: inline; filename=$name");
			header("Content-Description: PHP Generated Data");
			header('Content-Length: '.filesize($path));
			readfile($path);	
		} else {
			echo '<h1>File not found</h1>';
		}
				
		exit();
	}

	##
	public function response_session() {
		$session = session_id();
		echo $session;
		exit();
	}
	
	##
	public static function clear() {
		$ses = session_id();
		$tbl = static::table();
		$sql = "DELETE FROM {$tbl} WHERE parent='0' AND session='{$ses}'";		
		schemadb::execute('query',$sql);				
	}
	
	##
	public static function attachTo($id) {		
		$ses = session_id();
		//$tbl = static::table();
		$all = static::query(array(
			'parent'	=> 0,
			'session'	=> $ses,
		));
		foreach($all as $row) {
			$p0 = $row->getPath();
			$row->parent = $id;
			$row->store();
			$p1 = $row->getPath();			
			$b1 = dirname($p1);
			if (!is_dir($b1)) {
				mkdir($b1,0777,true);
			}
			$err = rename($p0,$p1);			
		}
		//$sql = "UPDATE {$tbl} SET parent='{$id}' WHERE parent='0' AND session='{$ses}'";		
		//schemadb::execute('query',$sql);				
	}
	
	##
	public function getPath() {
		return str_replace(array(
			'{base}',
			'{folder}',
			'{parent}',
			'{name}'
		),array(
			$this->base,
			$this->folder,
			(int)$this->parent,
			$this->name			
		),$this->path);		
	}
}
