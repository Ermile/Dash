<?php
namespace content_account\main;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	public function repository()
	{
		if(!$this->login())
		{
			$this->redirector($this->url('base'). '/enter')->redirect();
			return;
		}

		if(!SubDomain)
		{
			\lib\error::page(T_("SubDomain not found"));
		}
	}
}
?>