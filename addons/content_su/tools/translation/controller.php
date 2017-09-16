<?php
namespace addons\content_su\tools\translation;

class controller extends \addons\content_su\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->getUpdates();
		$this->get()->ALL();
	}

	function getUpdates()
	{

		$exist    = true;
		$mypath   = \lib\utility::get('path');
		$myupdate = \lib\utility::get('update');
		if($mypath)
		{
			echo \lib\utility\twigTrans::extract($mypath, $myupdate);
			exit();
		}
	}
}
?>