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
			$this->redirector(\lib\url::base(). '/enter')->redirect();
			return;
		}

	}
}
?>