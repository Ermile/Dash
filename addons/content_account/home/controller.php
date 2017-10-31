<?php
namespace content_account\home;

class controller extends \content_account\main\controller
{
	/**
	 * rout
	 */
	function ready()
	{
		// list of all team the user is them
		$this->get(false, 'dashboard')->ALL();
	}
}
?>