<?php
namespace addons\content_su\sample;

class controller extends \addons\content_su\home\controller
{

	function _route()
	{
		// $this->get()->all();
		// check permission to access to su
		parent::_permission('su');

		$addr = \lib\router::get_url(1);

		if(!$addr)
		{
			return;
		}

		if(is_file(addons.'content_su/sample/template/'.$addr.'.html'))
		{
			$this->display_name     = 'content_su/sample/template/'.$addr.'.html';
			$this->route_check_true = true;
			$this->get()->ALL();
		}
	}

}
?>