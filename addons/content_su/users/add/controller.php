<?php
namespace addons\content_su\users\add;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		$this->get(false, "add")->ALL();

		$this->post('add')->ALL();
		$this->post('add')->ALL("/users\/add\/(\d+)/");
	}
}
?>