<?php
namespace content_account\profile;

class controller extends \content_account\main\controller
{
	/**
	 * rout
	 */
	function ready()
	{

		$this->get(false, 'profile')->ALL('profile');
		$this->post('profile')->ALL('profile');
	}
}
?>