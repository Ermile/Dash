<?php
namespace content_su\transactions;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->get()->ALL();

	}
}
?>