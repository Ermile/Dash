<?php
namespace addons\content_su\backup;
use \lib\utility;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$config_backup = @\lib\utility\file::read(database. 'backup/schedule');
		if($config_backup && is_string($config_backup))
		{
			$config_backup = json_decode($config_backup, true);
			$this->data->config_backup = $config_backup;
		}
		
	}
}
?>