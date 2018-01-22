<?php
namespace addons\content_cp\attachments\home;


class controller extends \addons\content_cp\main\controller
{
	function ready()
	{
		$this->get()->ALL();
	}
}
?>