<?php
namespace lib\app\user;
use \lib\utility;
use \lib\debug;

trait add
{

	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{
		\lib\app::variable($_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		$default_option =
		[
			'save_log' => true,
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\lib\user::id())
		{
			if($_option['save_log']) \lib\app::log('api:user:user_id:notfound', null, $log_meta);
			if($_option['debug']) debug::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		$return         = [];

		$args['status'] = 'awaiting';

		$user_id        = \lib\db\users::signup($args);

		if(!$user_id)
		{
			if($_option['save_log']) \lib\app::log('api:user:no:way:to:insert:user', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("No way to insert user"), 'db', 'system');
			return false;
		}

		$return['user_id'] = $_args['user_id'] = \lib\utility\shortURL::encode($user_id);

		// \lib\app\contact::add($_args);


		if(debug::$status)
		{
			if($_option['debug']) debug::true(T_("User successfuly added"));
		}

		return $return;
	}
}
?>