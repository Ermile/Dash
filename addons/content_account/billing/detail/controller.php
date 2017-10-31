<?php
namespace content_account\billing\detail;

class controller extends  \content_account\main\controller
{

	public function ready()
	{


		$this->get("detail", "detail")->ALL("/^billing\/detail$/");

		$this->post("detail")->ALL("/^billing\/detail$/");
	}
}
?>