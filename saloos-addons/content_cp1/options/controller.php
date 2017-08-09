<?php
namespace addons\content_cp\options;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// check permission to access to cp
		parent::_permission('cp');

		//allow put on profile
		// $this->display_name	= 'content_cp/templates/module_options.html';
		$this->get(null, 'datatable')->ALL('options');
		$this->put('options')->ALL('options');


		// $result = \lib\utility\sms::send(9893569759, 'تست');
		// var_dump($result);exit();

	}
}
?>