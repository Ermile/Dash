<?php
namespace addons\content_cp\posts;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// check permission to access to cp
		parent::_permission('cp');
		// find best display
		parent::cpFindDisplay();

	}
}
?>