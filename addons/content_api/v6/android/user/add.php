<?php
namespace content_api\v6\android\user;


class add
{
	private static $load_user = [];
	private static $response  = [];
	private static $user_id   = null;
	private static $zoneid    = null;


	public static function add()
	{
		\content_api\v6::check_token();

		$add_user = self::check_input();

		if(!$add_user || !is_array($add_user))
		{
			\content_api\v6::no(400);
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

		$token = md5($token);

		$add_user['meta'] = json_encode($add_user['meta'], JSON_UNESCAPED_UNICODE);

		if(self::user_exist($token))
		{
			self::user_update($add_user);
		}
		else
		{
			$add_user['uniquecode'] = $token;
			self::user_add($add_user);
		}

		if(self::$user_id)
		{
			$apikey = \dash\app\user_auth::make_user_auth(self::$user_id, 'android', self::$zoneid);
			self::$response['apikey'] = $apikey;
		}

		self::$response['zoneid'] = 'android-'. \dash\coding::encode(self::$zoneid);

		\content_api\v6::bye(self::$response);
	}

	private static function check_input()
	{
		$model = \dash\request::post('model');

		if(!$model)
		{
			\dash\notif::error(T_("Model"). ' '. T_("not set"));
			return false;
		}

		$model = mb_strtolower($model);

		if(mb_strlen($model) > 100)
		{
			\dash\notif::error(T_("model"). ' '. T_("is out of range"));
			return false;
		}

		$serial = \dash\request::post('serial');

		if(!$serial)
		{
			\dash\notif::error(T_("Serial"). ' '. T_("not set"));
			return false;
		}

		$serial = mb_strtolower($serial);

		if(mb_strlen($serial) > 100)
		{
			\dash\notif::error(T_("serial"). ' '. T_("is out of range"));
			return false;
		}

		$manufacturer = \dash\request::post('manufacturer');

		if(!$manufacturer)
		{
			\dash\notif::error(T_("Manufacturer"). ' '. T_("not set"));
			return false;
		}

		$manufacturer = mb_strtolower($manufacturer);

		if(mb_strlen($manufacturer) > 100)
		{
			\dash\notif::error(T_("manufacturer"). ' '. T_("is out of range"));
			return false;
		}

		$version = \dash\request::post('version');
		if(!$version)
		{
			\dash\notif::error(T_("Version"). ' '. T_("not set"));
			return false;
		}

		$version = mb_strtolower($version);
		if(mb_strlen($version) > 20)
		{
			\dash\notif::error(T_("version"). ' '. T_("is out of range"));
			return false;
		}

		$hardware = \dash\request::post('hardware');
		if(mb_strlen($hardware) > 50)
		{
			\dash\notif::error(T_("hardware"). ' '. T_("is out of range"));
			return false;
		}

		$type = \dash\request::post('type');
		if(mb_strlen($type) > 50)
		{
			\dash\notif::error(T_("type"). ' '. T_("is out of range"));
			return false;
		}

		$board = \dash\request::post('board');
		if(mb_strlen($board) > 100)
		{
			\dash\notif::error(T_("board"). ' '. T_("is out of range"));
			return false;
		}

		$id = \dash\request::post('id');
		if(mb_strlen($id) > 100)
		{
			\dash\notif::error(T_("id"). ' '. T_("is out of range"));
			return false;
		}

		$product = \dash\request::post('product');
		if(mb_strlen($product) > 100)
		{
			\dash\notif::error(T_("product"). ' '. T_("is out of range"));
			return false;
		}

		$device = \dash\request::post('device');
		if(mb_strlen($device) > 100)
		{
			\dash\notif::error(T_("device"). ' '. T_("is out of range"));
			return false;
		}

		$brand = \dash\request::post('brand');
		if(mb_strlen($brand) > 100)
		{
			\dash\notif::error(T_("brand"). ' '. T_("is out of range"));
			return false;
		}

		$add_user                     = [];
		$add_user['model']            = $model;
		$add_user['serial']           = $serial;
		$add_user['manufacturer']     = $manufacturer;
		$add_user['version']          = $version;

		$add_user['meta']             = [];
		$add_user['meta']['hardware'] = $hardware;
		$add_user['meta']['type']     = $type;
		$add_user['meta']['board']    = $board;
		$add_user['meta']['id']       = $id;
		$add_user['meta']['product']  = $product;
		$add_user['meta']['device']   = $device;
		$add_user['meta']['brand']    = $brand;

		return $add_user;
	}


	private static function user_exist($_token)
	{
		$load = \dash\db\user_android::get(['uniquecode' => $_token, 'limit' => 1]);

		if(isset($load['user_id']))
		{
			if(isset($load['id']))
			{
				self::$zoneid = $load['id'];
			}

			self::$user_id = $load['user_id'];

			self::$response['usercode'] = \dash\coding::encode($load['user_id']);

			self::$load_user = $load;

			return $load;
		}
		return false;
	}


	private static function user_update($_detail)
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
			self::$zoneid = \dash\db::insert_id();
		}

		self::$response['usercode'] = \dash\coding::encode($user_id);

		\dash\log::set('ApiApplicationAddUser', ['code' => $user_id]);
	}
}
?>