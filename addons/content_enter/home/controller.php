<?php
namespace addons\content_enter\home;


class controller
{
	public static function routing()
	{
		// if the user login redirect to base
		if(\lib\permission::access('enter:another:session'))
		{
			// the admin can login by another session
			// never redirect to main
		}
		else
		{
			if(\lib\user::login())
			{
				\lib\redirect::to(\lib\url::base());
				return;
			}
		}

		// save all param-* | param_* in $_GET | $_POST
		self::save_param();

		// save referer
		// to redirect the user ofter login or signup on the referered address
		if(\lib\request::get('referer') && \lib\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \lib\request::get('referer');
		}
	}


	/**
	 * Saves a parameter.
	 * save all param-* in url into the session
	 *
	 */
	public static function save_param()
	{
		$param = $_REQUEST;

		if(!is_array($param))
		{
			$param = [];
		}

		$save_param = [];

		foreach ($param as $key => $value)
		{
			if(substr($key, 0, 5) === 'param')
			{
				$save_param[substr($key, 6)] = $value;
			}
		}

		if(!empty($save_param))
		{
			$_SESSION['param'] = $save_param;
		}
	}
}
?>