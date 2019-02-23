<?php
namespace content_api\v5\user;


class user_add
{
	private static $load_user     = [];
	private static $response      = [];
	private static $user_id       = null;
	private static $x_app_request = null;


	public static function add()
	{
		$post     = \dash\request::post();

		$add_user = [];
		$meta     = [];
		$i        = 0;

		$v5 = \content_api\v5::$v5;

		if(isset($v5['x_app_request']))
		{
			self::$x_app_request = $v5['x_app_request'];
		}

		if(!in_array(self::$x_app_request, ['android']))
		{
			// \dash\log::set('invalidXAppRequestAPI');
			\dash\header::status(400, T_("Invalid x-app-request"));
		}

		$add_user['model']        = null;
		$add_user['serial']       = null;
		$add_user['manufacturer'] = null;
		$add_user['version']      = null;

		foreach ($post as $key => $value)
		{
			// check to not save a lot of detail!
			$i++;
			if($i > 50)
			{
				break;
			}

			if(mb_strlen($value) >= 200)
			{
				$value = substr($value, 0, 199);
			}

			$myField = mb_strtolower($key);

			switch ($myField)
			{
				case 'model':
				case 'manufacturer':
					$add_user[$myField] = mb_strtolower($value);
					$meta[$myField] = $value;
					break;

				case 'serial':
				case 'version':
					$add_user[$myField] = $value;
					$meta[$myField] = $value;
					break;

				default:
					$meta[$myField] = $value;
					break;
			}
		}

		$add_user['lastupdate'] = date("Y-m-d H:i:s");

		$token  = 'APP_';
		$token .= $add_user['model'];
		$token .= '_';
		$token .= $add_user['serial'];
		$token .= '_';
		$token .= $add_user['manufacturer'];
		$token .= '_';
		$token .= $add_user['version'];

		// empty args
		if($token === 'APP____')
		{
			// \dash\log::set('emptyAndroidDetail');
			\dash\header::status(400, T_("Empty input values"));
		}

		$meta['usertoken_raw'] = $token;

		$token = md5($token);

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);

		$add_user['meta']   = $meta;

		$sended_token = self::sended_token();

		if($sended_token)
		{
			if(self::user_exist($sended_token))
			{
				self::user_update($token, $add_user);
			}
			else
			{
				$add_user['uniquecode'] = $token;
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
				$add_user['uniquecode'] = $token;
				self::user_add($add_user);
			}
		}

		if(self::$user_id)
		{
			$user_auth = \dash\app\user_auth::make_user_auth(self::$user_id, self::$x_app_request);
			self::$response['auth3'] = $user_auth;

		}

		self::$response['usertoken'] = $token;

		\content_api\v5::end5(self::$response);
	}


	private static function sended_token()
	{
		$sended_token = \dash\request::post('app_token');
		return $sended_token;
	}


	private static function user_exist($_token)
	{
		$load = \dash\db\user_android::get(['uniquecode' => $_token, 'limit' => 1]);

		if(isset($load['user_id']))
		{
			self::$user_id = $load['user_id'];

			self::$response['usercode'] = \dash\coding::encode($load['user_id']);

			self::$load_user = $load;
			return $load;
		}
		return false;
	}


	private static function user_update($_token, $_detail)
	{
		if(isset(self::$load_user['id']))
		{
			\dash\db\user_android::update($_detail, self::$load_user['id']);
		}
	}


	private static function user_add($_detail)
	{
		$user_id = \dash\db\users::signup();
		if($user_id)
		{
			self::$user_id = $user_id;
			$_detail['user_id'] = $user_id;
			\dash\db\user_android::insert($_detail);
		}

		self::$response['usercode'] = \dash\coding::encode($user_id);

		\dash\log::set('ApiApplicationAddUser', ['code' => $user_id]);
	}
}
?>