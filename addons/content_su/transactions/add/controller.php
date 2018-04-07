<?php
namespace content_su\transactions\add;


class controller extends \addons\content_su\transactions\controller
{
	public function ready()
	{
		parent::ready();
		$this->get()->ALL();
		$this->post('add')->ALL();
	}
}
?>