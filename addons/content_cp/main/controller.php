<?php
namespace addons\content_cp\main;

class controller extends \mvc\controller
{

	public function repository()
	{

		if(!\lib\user::login())
		{
			\lib\redirect::to(\lib\url::base(). '/enter?referer='. \lib\url::pwd());
			return;
		}

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(\lib\url::isLocal())
		{
			// on tld dev open the cp to upgrade for test
		}
		else
		{
			if(\lib\permission::access('cp'))
			{
				// the user have permission of cp
			}
			else
			{
				\lib\error::access(T_("Can not access to cp"));
			}
		}
	}
}
?>