<?php
namespace addons\content_cp\transactions\manage;

class controller extends \mvc\controller
{
	public function _route()
	{

		\lib\permission::access('cp:transaction:manage', 'block');
		$this->get(false, "manage")->ALL();
		$this->post('manage')->ALL();
	}
}
?>