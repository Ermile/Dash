<?php
namespace addons\content_cp\users\detail;

class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{
		\lib\permission::access('cp:user:detail', 'block');

		$this->get(false, "detail")->ALL();

		$this->get("load", "detail")->ALL("/^users\/detail\/([a-zA-Z0-9]+)$/");

		$this->post('detail')->ALL();
		$this->post('detail')->ALL("/^users\/detail\/([a-zA-Z0-9]+)$/");
	}
}
?>