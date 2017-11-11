<?php
namespace lib\app\user;
use \lib\debug;

trait get
{


	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function get($_args, $_options = [])
	{
		\lib\app::variable($_args);

		$default_options =
		[
			'debug' => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if($_options['debug'])
		{
			debug::title(T_("Operation Faild"));
		}

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		if(!\lib\user::id())
		{
			// return false;
		}

		$id = \lib\app::request("id");
		$id = \lib\utility\shortURL::decode($id);

		$shortname = \lib\app::request('shortname');

		if(!$id && !$shortname)
		{
			if($_options['debug'])
			{
				\lib\app::log('api:user:id:shortname:not:set', \lib\user::id(), $log_meta);
				debug::error(T_("User id or shortname not set"), 'id', 'arguments');
			}
			return false;
		}

		if($id && $shortname)
		{
			\lib\app::log('api:user:id:shortname:together:set', \lib\user::id(), $log_meta);
			if($_options['debug'])
			{
				debug::error(T_("Can not set user id and shortname together"), 'id', 'arguments');
			}
			return false;
		}

		if($id)
		{
			$result = \lib\db\users::access_user_id($id, \lib\user::id(), ['action' => 'view']);
		}
		else
		{
			$result = \lib\db\users::access_user($shortname, \lib\user::id(), ['action' => 'view']);
		}

		if(!$result)
		{
			if($id)
			{
				$result = \lib\db\users::get(['id' => $id, 'limit' => 1]);
			}
			elseif($shortname)
			{
				$result = \lib\db\users::get(['shortname' => $shortname, 'limit' => 1]);
			}

			if($result)
			{
				if(\lib\permission::access('load:all:user', null, \lib\user::id()))
				{
					$result = $result;
				}
				else
				{
					\lib\temp::set('user_access_denied', true);
					\lib\temp::set('user_exist', true);
					$result = false;
				}
			}
		}

		if(!$result)
		{
			\lib\app::log('api:user:access:denide', \lib\user::id(), $log_meta);
			if($_options['debug'])
			{
				debug::error(T_("Can not access to load this user details"), 'user', 'permission');
			}
			return false;
		}

		if($_options['debug'])
		{
			debug::title(T_("Operation complete"));
		}

		$result = self::ready($result);

		return $result;
	}
}
?>