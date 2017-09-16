<?php
namespace addons\content_su\users\add;

class controller extends \mvc\controller
{
	public function _route()
	{
		\lib\permission::access('su:user:add', 'block');

		$this->get(false, "add")->ALL();

		$this->post('add')->ALL();
		$this->post('add')->ALL("/users\/add\/(\d+)/");
	}
}
?>