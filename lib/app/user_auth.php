<?php
namespace dash\app;


class user_auth
{
	private static function generate_auth($_user_id = null)
	{
		$string = '';
		$string .= 'Ermile';
		$string .= (string) time();
		$string .= 'Dash';
		$string .= (string) rand();
		$string .= 'Api';
		$string .= (string) rand();
		$string .= 'Token';
		$string .= '_';
		$string .= $_user_id;
		$string .= '_';
		$string .= (string) microtime();
		$string = md5($string);
		return $string;
	}

	public static function make($_args = [])
	{
		$auth                  = self::generate_auth();
		$date_now              = date("Y-m-d H:i:s");
		$insert                = [];
		$insert['auth']        = $auth;
		$insert['user_id']     = null;
		$insert['status']      = 'enable';
		$insert['gateway']     = isset($_args['gateway']) ? $_args['gateway'] : null;
		$insert['parent']      = isset($_args['parent']) ? $_args['parent'] : null;
		$insert['type']        = 'guest';
		$insert['datecreated'] = $date_now;

		$insert = \dash\db\user_auth::insert($insert);
		if($insert)
		{
			$result           = [];
			$result['token']  = $auth;
			$result['create'] = $date_now;
			$result['expire'] = date("Y-m-d H:i:s", (time() + (60*3)));
			return $result;
		}
		return false;
	}

	public static function get_apikey($_user_id, $_gateway = null, $_gateway_id = null)
	{
		$check =
		[
			'user_id'    => $_user_id,
			'type'       => 'member',
			'status'     => 'enable',
			'gateway'    => $_gateway,
			'gateway_id' => $_gateway_id,
			'limit'      => 1,
		];

		$check = \dash\db\user_auth::get($check);

		if(isset($check['auth']))
		{
			return $check;
		}
		return null;
	}

	public static function get_appkey($_user_id)
	{
		$check =
		[
			'user_id'    => $_user_id,
			'type'       => 'appkey',
			'status'     => 'enable',
			'gateway'    => null,
			'gateway_id' => null,
			'limit'      => 1,
		];

		$check = \dash\db\user_auth::get($check);

		if(isset($check['auth']))
		{
			return $check;
		}
		return null;
	}


	public static function check_appkey($_appkey)
	{
		$check =
		[
			'auth'       => $_appkey,
			'type'       => 'appkey',
			'status'     => 'enable',
			'gateway'    => null,
			'gateway_id' => null,
			'limit'      => 1,
		];

		$check = \dash\db\user_auth::get($check);

		if(isset($check['auth']))
		{
			return $check;
		}
		return false;
	}


	public static function make_appkey($_user_id)
	{
		$check =
		[
			'user_id'    => $_user_id,
			'type'       => 'appkey',
			'status'     => 'enable',
			'gateway'    => null,
			'gateway_id' => null,
			'limit'      => 1,
		];

		$check = \dash\db\user_auth::get($check);

		if(isset($check['auth']))
		{
			return $check['auth'];
		}
		else
		{
			$auth = self::generate_auth($_user_id);

			$insert =
			[
				'user_id'     => $_user_id,
				'type'        => 'appkey',
				'status'      => 'enable',
				'gateway'     => null,
				'gateway_id'  => null,
				'datecreated' => date("Y-m-d H:i:s"),
				'auth'        => $auth,
			];

			$insert = \dash\db\user_auth::insert($insert);

			if($insert)
			{
				return $auth;
			}

			return false;
		}
	}


	public static function disable_api_key()
	{
		$get = self::get_apikey(...func_get_args());
		if(isset($get['id']))
		{
			\dash\db\user_auth::update(['status' => 'disable'], $get['id']);
			return true;
		}
		return false;
	}

	public static function make_user_auth($_user_id, $_gateway = null, $_gateway_id = null)
	{
		$check =
		[
			'user_id'    => $_user_id,
			'type'       => 'member',
			'status'     => 'enable',
			'gateway'    => $_gateway,
			'gateway_id' => $_gateway_id,
			'limit'      => 1,
		];

		$check = \dash\db\user_auth::get($check);

		if(isset($check['auth']))
		{
			return $check['auth'];
		}
		else
		{
			$auth = self::generate_auth($_user_id);

			$insert =
			[
				'user_id'     => $_user_id,
				'type'        => 'member',
				'status'      => 'enable',
				'gateway'     => $_gateway,
				'gateway_id'  => $_gateway_id,
				'datecreated' => date("Y-m-d H:i:s"),
				'auth'        => $auth,
			];

			$insert = \dash\db\user_auth::insert($insert);

			if($insert)
			{
				return $auth;
			}

			return false;
		}
	}
}
?>