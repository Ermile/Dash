<?php
namespace addons\content_cp\transactions\log;

class controller extends \mvc\controller
{
	public function _route()
	{

		\lib\permission::access('cp:transaction:log', 'block');

		$this->get(false, "log")->ALL("/transactions\/log\/id\=(\d+)/");

		$this->post('log')->ALL();
	}
}
?>