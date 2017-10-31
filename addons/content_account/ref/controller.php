<?php
namespace content_account\ref;

class controller extends  \content_account\main\controller
{

	public function ready()
	{

		$this->get(false, 'ref')->ALL();
	}
}
?>