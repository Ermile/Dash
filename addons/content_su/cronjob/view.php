<?php
namespace addons\content_su\cronjob;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->cronjob = \lib\utility\cronjob\options::status();
	}
}
?>