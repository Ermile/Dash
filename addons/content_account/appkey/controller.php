<?php
namespace content_account\appkey;

class controller extends  \content_account\main\controller
{

	public function ready()
	{
		$this->get()->ALL();
		$this->post('appkey')->ALL();
	}
}
?>