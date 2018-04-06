<?php
namespace addons\content_su\backup;
class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->post('backup')->ALL();
		$download = \dash\request::get('download');
		if($download)
		{
			\dash\file::download(database. 'backup/files/'. $download);
			return;
		}
	}
}
?>