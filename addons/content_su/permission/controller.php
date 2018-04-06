<?php
namespace addons\content_su\permission;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		if(\dash\url::directory() === 'permission')
		{
			\dash\header::status(404);
		}

		$this->get(false, "add")->ALL("/^permission\/([a-zA-Z0-9]+)$/");

		$this->post('add')->ALL("/^permission\/([a-zA-Z0-9]+)$/");
	}
}
?>