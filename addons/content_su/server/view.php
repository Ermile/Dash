<?php
namespace addons\content_su\server;


class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->my_server = $_SERVER;
	}
}
?>