<?php
namespace addons\content_su\transactions\add;

class controller extends \mvc\controller
{
	public function _route()
	{
		\lib\permission::access('su:transaction:add', 'block');

		$this->get(false, "add")->ALL();

		$this->get("load", "add")->ALL("/transactions\/add\/transactions\=(\d+)/");

		$this->post('add')->ALL();
		$this->post('add')->ALL("/transactions\/add\/transactions\=(\d+)/");
	}
}
?>