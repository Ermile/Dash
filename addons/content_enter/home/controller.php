<?php
namespace content_enter\home;


class controller
{
	public static function routing()
	{
		// if the user login redirect to base
		if(\dash\permission::check('EnterByAnother'))
		{
			// the admin can login by another session
			// never redirect to main
		}
		else
		{
			if(\dash\user::login())
			{
				$url = \dash\url::kingdom();
				if(\dash\option::config('redirect'))
				{
					$url .= '/'. \dash\option::config('redirect');
				}

				\dash\redirect::to($url);
				return;
			}
		}

		// save all param-* | param_* in $_GET | $_POST
		self::save_param();


		\dash\utility\hive::set(true);
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