<?php
namespace addons\content_cp\sample;

class controller extends \addons\content_cp\home\controller
{

	function _route()
	{
		// $this->get()->all();
		// check permission to access to cp
		parent::_permission('cp');

		return;
	}
}
?>