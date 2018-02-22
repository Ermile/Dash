<?php
namespace addons\content_su\backup;
class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->post('backup')->ALL();
		$download = \lib\utility::get('download');
		if($download)
		{
			\lib\utility\file::download(database. 'backup/files/'. $download);
			return;
		}
	}
}
?>