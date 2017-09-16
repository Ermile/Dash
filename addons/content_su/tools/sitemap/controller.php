<?php
namespace addons\content_su\tools\sitemap;

class controller extends \addons\content_su\home\controller
{
	public function _route()
	{
		parent::_route();

		$this->get()->ALL();
	}
}
?>