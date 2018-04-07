<?php
namespace content_cp\comments\home;


class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{
		$this->get()->ALL();
	}
}
?>