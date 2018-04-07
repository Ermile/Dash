<?php
namespace content_su\cronjob;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->cronjob = \dash\engine\cronjob\options::status();
	}
}
?>