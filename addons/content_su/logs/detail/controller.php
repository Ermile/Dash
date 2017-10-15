<?php
namespace addons\content_su\logs\detail;

class controller extends \addons\content_su\main\controller
{

	public function ready()
	{
		parent::ready();
		$url = \lib\router::get_url();

		$this->get(false, "detail")->ALL("/^logs\/detail\/(\d+)$/");

	}
}
?>