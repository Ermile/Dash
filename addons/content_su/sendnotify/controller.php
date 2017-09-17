<?php
namespace addons\content_su\sendnotify;
class controller extends \addons\content_su\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->post('nofity')->ALL();
	}
}
?>