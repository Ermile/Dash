<?php
namespace content_cp\posts\home;


class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{
		$this->get()->ALL();
	}
}
?>