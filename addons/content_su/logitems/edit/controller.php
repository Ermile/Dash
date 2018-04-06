<?php
namespace addons\content_su\logitems\edit;

class controller extends \addons\content_su\main\controller
{

	public function ready()
	{
		parent::ready();

		$url = \dash\url::directory();

		$this->get(false, "edit")->ALL("/^logitems\/edit\/(\d+)$/");
		$this->post("edit")->ALL("/^logitems\/edit\/(\d+)$/");

	}
}
?>