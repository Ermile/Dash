<?php
namespace addons\content_cp\users\add;

class controller extends \mvc\controller
{
	public function ready()
	{
		\lib\permission::access('cp:user:add', 'block');

		$this->get(false, "add")->ALL();

		$this->post('add')->ALL();
		$this->post('add')->ALL("/users\/add\/(\d+)/");
	}
}
?>