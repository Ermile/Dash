<?php
namespace addons\content_cp\permission;

class controller extends \addons\content_cp\main\controller
{
	public function _route()
	{
		\lib\permission::access('cp:permission:add', 'block');

		if(\lib\router::get_url() === 'permission')
		{
			\lib\error::page();
		}

		$this->get(false, "add")->ALL("/^permission\/([a-zA-Z0-9]+)$/");

		$this->post('add')->ALL("/^permission\/([a-zA-Z0-9]+)$/");
	}
}
?>