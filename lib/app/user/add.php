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
			'force_add'      => false,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!$_option['force_add'])
		{
			if(!\dash\user::id())
			{
				if($_option['save_log']) \dash\app::log('api:user:user_id:notfound', null, $log_meta);
				if($_option['debug']) \dash\notif::error(T_("User not found"), 'user');
				return false;
			}
		}

		// check args
		$args = self::check($_option);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return         = [];

		if(!$args['status'])
		{
			$args['status'] = 'awaiting';
		}

		if($args['mobile'])
		{
			$check_mobile_exist = \dash\db\users::get_by_mobile($args['mobile']);
			if(isset($check_mobile_exist['id']))
			{
				\dash\notif::error(T_("Duplicate mobile"), 'mobile');
				return false;
			}
		}

		$user_id = \dash\db\users::signup($args);

		if(!$user_id)
		{
			if($_option['save_log']) \dash\app::log('api:user:no:way:to:insert:user', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("No way to insert user"), 'db', 'system');
			return false;
		}

		$return['id']      = \dash\coding::encode($user_id);
		$return['user_id'] = \dash\coding::encode($user_id);
		\dash\log::set('addNewUser', ['data' => $user_id, 'datalink' => $return['user_id']]);
		// $_option['user_id'] = $user_id;

		// if($_option['contact'])
		// {
		// 	\dash\app\contact::merge($_args, $_option);
		// }

		if(\dash\engine\process::status())
		{
			if($_option['debug']) \dash\notif::ok(T_("User successfuly added"));
		}

		return $return;
	}
}
?>