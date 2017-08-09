<?php
namespace lib\mvc\controllers;

trait login
{
	/**
	 * Return login status without parameter
	 * If you pass the name as all return all of user session
	 * If you pass specefic user data field name return it
	 * @param  [type] $_name [description]
	 * @return [type]        [description]
	 */
	public function login($_name = null)
	{
		if(isset($_name))
		{
			if($_name === "all")
			{
				return isset($_SESSION['user'])? $_SESSION['user']: null;
			}
			else
			{
				return isset($_SESSION['user'][$_name])? $_SESSION['user'][$_name]: null;
			}
		}

		if(isset($_SESSION['user']['id']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	* check is set remember of this user and login by this
	*
	*/
	public function check_remeber_login()
	{
		$url = \lib\utility\safe::safe($_SERVER['REQUEST_URI']);

		// if(\lib\db\sessions::get_cookie() && !$this->login())
		// {
		// 	$user_id = \lib\db\sessions::get_user_id();

		// 	if($user_id && is_numeric($user_id))
		// 	{
		// 		// set user id in static var
		// 		self::$user_id = $user_id;
		// 		// load user data by user id
		// 		self::load_user_data('user_id');
		// 		// set login session
		// 		self::enter_set_login($url);
		// 	}
		// }
	}
}
?>