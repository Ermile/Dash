<?php
namespace content_api\v6\enter;


class controller
{
	private static $usercode;
	private static $usertoken;
	private static $mobile;
	private static $verifycode;
	private static $x_app_request;
	private static $user_id;
	private static $user_android;
	private static $mobile_user_id;

	private static $life_time = 60 * 5;

	use \content_api\v6\enter\login;
	use \content_api\v6\enter\verify;


	public static function routing()
	{
		\content_api\v6::check_token();

		$subchild = \dash\url::subchild();

		if(!$subchild)
		{
			self::login();
		}
		elseif($subchild === 'verify')
		{
			self::verify();
		}
		else
		{
			\content_api\v6::no(404);
		}

	}


	private static function check_true_user()
	{
		if(self::$x_app_request === 'android')
		{
			$get =
			[
				'user_id'    => self::$user_id,
				'uniquecode' => self::$usertoken,
				'limit'      => 1,
			];

			$load               = \dash\db\user_android::get($get);
			self::$user_android = $load;

			if(isset($load['id']))
			{
				self::check_last_update($load, 'user_android');
				return true;
			}
			else
			{
				\dash\log::set('API-InvalidUserCodeAndToken');
				\dash\notif::error(T_("Invalid usercode and usertoken"), ['element' => ['usercode', 'usertoken']]);
				return false;
			}
		}
		else
		{
			\dash\notif::error(T_("This method was not supported"));
			return false;
		}
	}


	private static function check_last_update($_data, $_table)
	{
		if(array_key_exists('lastupdate', $_data))
		{
			$need_update = false;

			if(!$_data['lastupdate'])
			{
				$need_update = true;
			}
			else
			{
				$lastupdate = strtotime($_data['lastupdate']);
				if((time() - $lastupdate) > (60*60*2))
				{
					$need_update = true;
				}
			}

			if($need_update)
			{
				\dash\db\user_android::update(['lastupdate' => date("Y-m-d H:i:s")], $_data['id']);
			}
		}
	}


	private static function check_input()
	{
		$mobile = \dash\request::post('mobile');
		if(!$mobile)
		{
			\dash\notif::error(T_("Mobile not set"), 'mobile');
			return false;
		}

		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Invalid mobile"), 'mobile');
			return false;
		}

		$usercode = \dash\header::get('usercode');
		if(!$usercode)
		{
			\dash\notif::error(T_("User code not set"), 'usercode');
			return false;
		}

		$user_id = \dash\coding::decode($usercode);
		if(!$user_id)
		{
			\dash\notif::error(T_("Invalid usercode"), 'usercode');
			return false;
		}


		$usertoken = \dash\header::get('usertoken');
		if(!$usertoken)
		{
			\dash\notif::error(T_("User token not set"), 'usertoken');
			return false;
		}

		if(mb_strlen($usertoken) !== 32)
		{
			\dash\notif::error(T_("Invalid usertoken"), 'usertoken');
			return false;
		}

		$verifycode = \dash\request::post('verifycode');
		if($verifycode)
		{
			if(!is_numeric($verifycode))
			{
				\dash\notif::error(T_("Invalid verifycode"), 'verifycode');
				return false;
			}

			$verifycode = intval($verifycode);

			if($verifycode < 10000 || $verifycode > 99999)
			{
				\dash\notif::error(T_("Verification code is out of range"), 'verifycode');
				return false;
			}

		}

		self::$x_app_request = $x_app_request;
		self::$mobile        = $mobile;
		self::$usercode      = $usercode;
		self::$user_id       = $user_id;
		self::$usertoken     = $usertoken;
		self::$verifycode   = $verifycode;


		return true;
	}
}
?>