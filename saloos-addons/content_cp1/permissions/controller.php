<?php
namespace addons\content_cp\permissions;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// check permission to access to cp
		parent::_permission('cp');

		// $this->route_check_true = true;
		$myChild = $this->child();
		if($myChild)
		{
			$this->display_name	= 'content_cp/permissions/display_child.html';
			switch ($myChild)
			{
				case 'add':
					$this->post($myChild)->ALL('permissions/add');
					break;

				case 'edit':
					$this->put($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					break;

				case 'delete':
					$this->post($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					$this->get($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					break;

				default:
					// $this->get()->ALL([
					// 		"max"=>3
					// 	]);
					$this->get()->ALL('/^[^\/]*\/[^\/]*$/');
					return false;
					break;
			}
		}
		else
		{

		}
	}
}
?>