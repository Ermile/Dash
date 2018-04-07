<?php
namespace content_su\users\edit;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		$this->get(false, "edit")->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");


		$this->post('edit')->ALL();
		$this->post('edit')->ALL("/^users\/edit\/([a-zA-Z0-9]+)$/");
	}
}
?>