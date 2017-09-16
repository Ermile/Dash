<?php
namespace addons\content_su\users\edit;

class controller extends \addons\content_su\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get(false, "edit")->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");


		$this->post('edit')->ALL();
		$this->post('edit')->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");
	}
}
?>