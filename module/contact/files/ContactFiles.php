<?php
##
require_once __BASE__.'/model/files/Files.php';

##
class ContactFiles extends Files
{
    ##

    public function __construct()
    {
        ##
        $this->base = __BASE__.'/store/Contact';
    }

    ##

    public function response()
    {
        ##
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $action = @$_GET['action'];
        $folder = @$_GET['folder'];
        $parent = (int) @$_GET['parent'];
        $session = @$_GET['session'];
        ##
        switch ($action) {

            case 'upload':
                $this->response_upload($folder, $parent, $session);
                break;

            case 'list':
                $this->response_list($folder, $parent);
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

    public function response_list($folder, $parent)
    {
        $all = static::query([
            'folder' => $folder,
            'parent' => $parent,
        ]);

        ?>
		<ul class="list-group">
			<?php foreach ($all as $row) {
    ?>
				<li class="list-group-item">
					<?=$row->name?>
                    <a href="<?=__HOME__?>/contact/importCsv/id/<?=$row->id?>" class="btn btn-xs btn-primary"><?=_('Import')?>&nbsp;</a>
					<button 
						data-ui-uploadify-delete="<?=$row->id?>" 
						class="btn btn-xs btn-danger"
						>
						&nbsp;<i class="glyphicon glyphicon-remove"></i>&nbsp;
					</button>
				</li>
			<?php 
}
        ?>	
		</ul>
		<?php	
    }
}
ContactFiles::schemadb_update();
