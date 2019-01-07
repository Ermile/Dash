<?php
namespace content_api\v5\user;


class user_add
{
	private static $load_user = [];
	private static $x_app_request = null;
	public static function add()
	{
		$post     = \dash\request::post();

		$add_user = [];
		$meta     = [];
		$i        = 0;

		$v5 = \content_api\controller::$v5;

		if(isset($v5['x_app_request']))
		{
			self::$x_app_request = $v5['x_app_request'];
		}

		if(!in_array(self::$x_app_request, ['android']))
		{
			\dash\header::status(400);
		}

		$add_user[self::$x_app_request. '_model']        = null;
		$add_user[self::$x_app_request. '_serial']       = null;
		$add_user[self::$x_app_request. '_manufacturer'] = null;
		$add_user[self::$x_app_request. '_version']      = null;

		foreach ($post as $key => $value)
		{
			// check to not save a lot of detail!
			$i++;
			if($i > 100)
			{
				break;
			}

			$myField = mb_strtolower($key);

			switch ($myField)
			{
				case 'model':
				case 'manufacturer':
					$add_user[self::$x_app_request. '_'. $myField] = mb_strtolower($value);
					$meta[$myField] = $value;
					break;

				case 'serial':
				case 'version':
					$add_user[self::$x_app_request. '_'. $myField] = $value;
					$meta[$myField] = $value;
					break;

				default:
					$meta[$myField] = $value;
					break;
			}
		}

		$add_user[self::$x_app_request. '_lastupdate'] = date("Y-m-d H:i:s");

		$token  = 'APP_';
		$token .= $add_user[self::$x_app_request. '_model'];
		$token .= '_';
		$token .= $add_user[self::$x_app_request. '_serial'];
		$token .= '_';
		$token .= $add_user[self::$x_app_request. '_manufacturer'];
		$token .= '_';
		$token .= $add_user[self::$x_app_request. '_version'];

		$meta[self::$x_app_request. '_user_token_raw'] = $token;

		$token = md5($token);

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);

		$add_user[self::$x_app_request. '_meta']   = $meta;

		$sended_token = self::sended_token();

		if($sended_token)
		{
			if(self::user_exist($sended_token))
			{
				self::user_update($token, $add_user);
			}
			else
			{
				$add_user[self::$x_app_request. '_uniquecode'] = $token;
				self::user_add($add_user);
			}
		}
		else
		{
			if(self::user_exist($token))
			{
				self::user_update($token, $add_user);
			}
			else
			{
				$add_user[self::$x_app_request. '_uniquecode'] = $token;
				self::user_add($add_user);
			}
		}

		\dash\notif::result(['usertoken' => $token]);
		\dash\code::end();

	}


	private static function sended_token()
	{
		$sended_token = \dash\request::post('app_token');
		return $sended_token;
	}


	private static function user_exist($_token)
	{
		$load = \dash\db\users::get([self::$x_app_request. '_uniquecode' => $_token, 'limit' => 1]);

		if(isset($load['id']))
		{
			self::$load_user = $load;
			return $load;
		}
		return false;
	}


	private static function user_update($_token, $_detail)
	{
		if(isset(self::$load_user['id']))
		{
			\dash\db\users::update($_detail, self::$load_user['id']);
		}
	}


	private static function user_add($_detail)
	{
		$user_id = \dash\db\users::signup($_detail);
		\dash\log::set('ApiApplicationAddUser', ['code' => $user_id]);
	}
}
?>