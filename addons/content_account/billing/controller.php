<?php
namespace content_account\billing;

class controller extends  \content_account\main\controller
{

	public function ready()
	{

		$this->get("billing", "billing")->ALL();
		$this->post("billing")->ALL();

		$url = \dash\url::directory();

		// handle invoice manually without regular and set_controller!
		// if(preg_match("/^billing\/invoice\/\d+$/", $url))
		// {
		// 	\dash\route-----r::set_controller('\\content_account\\billing\\invoice');
		// 	return;
		// }
	}
}
?>