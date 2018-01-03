<?php
namespace addons\content_cp\users\edit;

class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{
		\lib\permission::access('cp:user:edit', 'block');

		$this->get(false, "edit")->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");


		$this->post('edit')->ALL();
		$this->post('edit')->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");
	}
}
?>