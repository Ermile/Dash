<?php
namespace dash\utility;


class appkey
{
	public static $APP_KEY = null;


	/**
	 * get appkey from db
	 *
	 * @param      <type>  $_authorization  The authorization
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_authorization)
	{
		if(!self::$APP_KEY)
		{
			$arg =
			[
				'status' => 'enable',
				'value'  => $_authorization,
				'limit'  => 1,
			];

			$tmp = \dash\db\options::get($arg);
			self::$APP_KEY = $tmp;
		}

		return self::$APP_KEY;
	}


	/**
	 * get appkey data to show
	 */
	public static function get_app_key($_user_id)
	{
		$where =
		[
			'user_id' => $_user_id,
			'cat'     => 'appkey',
			// 'key'     => 'app_key_'. (string) $_user_id,
			'status'  => 'enable',
			'limit'   => 1
		];
		$app_key = \dash\db\options::get($where);

		if($app_key && isset($app_key['value']))
		{
			return $app_key['value'];
		}

		return null;
	}


	/**
	 * Creates an app key.
	 *
	 * @param      string  $_user_id  The user identifier
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public static function create_app_key($_user_id)
	{
		self::destroy_app_key($_user_id);

		$app_key = rand(3, 1001). "!~ERMILE~!". (string) $_user_id. ':_APP_KEY_:'. (string) time(). "*DASH*". rand(251, 1408);

		$app_key = \dash\utility::hasher($app_key, null, true);

		$app_key = \dash\safe::safe($app_key);

		$arg =
		[
			'user_id' => $_user_id,
			'cat'     => 'appkey',
			// 'key'     => 'app_key_'. (string) $_user_id,
			'value'   => $app_key
		];
		$set = \dash\db\options::insert($arg);

		if($set)
		{
			return $app_key;
		}
		return false;
	}


	/**
	 * destroy app keuy
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function destroy_app_key($_user_id)
	{
		$where =
		[
			'user_id' => $_user_id,
			'cat'     => 'appkey',
			// 'key'     => 'app_key_'. (string) $_user_id,
		];

		$set = ['status' => 'disable'];

		\dash\db\options::update_on_error($set, $where);
	}


	/**
	 * destroy appkey when log out
	 *
	 * @param      <type>  $_appkey  The appkey
	 */
	public static function destroy($_appkey)
	{
		$where =
		[
			'cat'   => 'appkey',
			'value' => $_appkey,
		];
		$set = ['status' => 'disable'];
		return \dash\db\options::update_on_error($set, $where);
	}
}
?>