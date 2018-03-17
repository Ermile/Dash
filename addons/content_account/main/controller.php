<?php
namespace content_account\main;

class controller extends \mvc\controller
{

	/**
	 * rout
	 */
	public function repository()
	{
		if(!\lib\user::login())
		{
			\lib\redirect::to(\lib\url::base(). '/enter');
			return;
		}

	}
}
?>