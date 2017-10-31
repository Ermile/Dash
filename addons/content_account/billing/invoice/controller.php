<?php
namespace content_account\billing\invoice;

class controller extends  \content_account\main\controller
{

	public function ready()
	{


		$this->get("invoice", "invoice")->ALL("/^billing\/invoice\/(\d+)$/");

		$this->post("invoice")->ALL("/^billing\/invoice\/(\d+)$/");
	}
}
?>