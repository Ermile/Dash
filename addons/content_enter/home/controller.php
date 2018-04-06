<?php
namespace content_enter\home;


class controller
{
	public static function routing()
	{
		// if the user login redirect to base
		if(\dash\permission::access('enter:another:session'))
		{
			// the admin can login by another session
			// never redirect to main
		}
		else
		{
			if(\dash\user::login())
			{
				\dash\redirect::to(\dash\url::base());
				return;
			}
		}

		// save all param-* | param_* in $_GET | $_POST
		self::save_param();

		// save referer
		// to redirect the user ofter login or signup on the referered address
		if(\dash\request::get('referer') && \dash\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \dash\request::get('referer');
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