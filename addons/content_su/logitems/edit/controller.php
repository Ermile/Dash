<?php
namespace addons\content_su\logitems\edit;

class controller extends \addons\content_su\main\controller
{

	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		$this->get(false, "edit")->ALL("/^logitems\/edit\/(\d+)$/");
		$this->post("edit")->ALL("/^logitems\/edit\/(\d+)$/");

	}
}
?>