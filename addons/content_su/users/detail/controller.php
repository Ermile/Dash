<?php
namespace addons\content_su\users\detail;

class controller extends \addons\content_su\main\controller
{
	public function _route()
	{
		\lib\permission::access('su:user:detail', 'block');

		$this->get(false, "detail")->ALL();

		$this->get("load", "detail")->ALL("/^users\/detail\/([a-zA-Z0-9]+)$/");

		$this->post('detail')->ALL();
		$this->post('detail')->ALL("/^users\/detail\/([a-zA-Z0-9]+)$/");
	}
}
?>