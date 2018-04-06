<?php
namespace addons\content_enter\main;

class controller extends \mvc\controller
{
	use _use;
	public function repository()
	{
		$url = \dash\url::directory();
		// /main can not route
		if($url === 'main')
		{
			\dash\header::status(404, T_("Unavalible"));
		}

		// // redirect subdomain to main domain on enter
		// if(\dash\url::subdomain())
		// {
		// 	// ---------------------------------------------- temporary, fix this
		// 	$mainEnter = \dash\url::protocol().'://'. \dash\url::domain().'/enter';
		// 	\dash\redirect::to($mainEnter);
		// }
	}


	/**
	* if the user is login redirect to base
	*/
	public function if_login_not_route()
	{
		if(\dash\user::login())
		{
			self::go_to(\dash\url::base());
		}
	}


	/**
	* if login route
	*/
	public function if_login_route()
	{
		if(!\dash\user::login())
		{
			self::go_to(\dash\url::base());
		}
	}


	/**
	* check is set remeber me of this user
	*/
	public function check_remember_me()
	{
		if(\dash\db\sessions::get_cookie() && !\dash\user::login())
		{
			$user_id = \dash\db\sessions::get_user_id();

			if($user_id && is_numeric($user_id))
			{
				// set user id in static var
				self::$user_id = $user_id;
				// load user data by user id
				self::load_user_data('user_id');
				// set login session
				self::enter_set_login(null, true);
			}
		}
	}
}
?>