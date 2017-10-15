<?php
namespace addons\content_su\transactions\add;

class controller extends \addons\content_su\transactions\controller
{
	public function ready()
	{
		parent::ready();

		$this->get(false, "add")->ALL();

		$this->get(false, "add")->ALL("/transactions\/add\/transactions\=(\d+)/");

		$this->post('add')->ALL();
		$this->post('add')->ALL("/transactions\/add\/transactions\=(\d+)/");
	}
}
?>