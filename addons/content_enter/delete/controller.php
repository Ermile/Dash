<?php
namespace content_enter\delete;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		$this->get()->ALL();
		$this->post('delete')->ALL();
	}
}
?>