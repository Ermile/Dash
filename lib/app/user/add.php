<?php
namespace dash\app\user;


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
		\dash\app::variable($_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\app::request(),
			]
		];

		$default_option =
		[
			'save_log'       => true,
			'contact'        => true,
			'debug'          => true,
			'other_field'    => null,
			'other_field_id' => null,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\dash\user::id())
		{
			if($_option['save_log']) \dash\app::log('api:user:user_id:notfound', null, $log_meta);
			if($_option['debug']) \dash\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return         = [];

		$args['status'] = 'awaiting';

		$user_id        = self::find_user_id($args, $_option, false, null);

		if(!$user_id)
		{
			if($_option['save_log']) \dash\app::log('api:user:no:way:to:insert:user', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("No way to insert user"), 'db', 'system');
			return false;
		}

		$return['user_id'] = \dash\coding::encode($user_id);

		$_option['user_id'] = $user_id;

		if($_option['contact'])
		{
			\dash\app\contact::merge($_args, $_option);
		}

		if(\dash\engine\process::status())
		{
			if($_option['debug']) \dash\notif::ok(T_("User successfuly added"));
		}

		return $return;
	}
}
?>