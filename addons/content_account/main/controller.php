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
			\lib\redirect::to(\lib\url::base(). '/enter');
			return;
		}

	}
}
?>