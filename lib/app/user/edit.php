<?php
namespace dash\app\user;


trait edit
{
	/**
	 * edit a user
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function edit($_args, $_option = [])
	{
		\dash\app::variable($_args);

		$default_option =
		[
			'other_field'    => null,
			'other_field_id' => null,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\app::request(),
			]
		];

		// check args
		$args = self::check($_option);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$id = \dash\app::request('id');
		$id = \dash\coding::decode($id);

		if(!$id)
		{
			\dash\notif::error(T_("Can not access to edit staff"), 'staff');
			return false;
		}

		$load_user = \dash\db\users::get_by_id($id);
		if(!isset($load_user['id']))
		{
			\dash\notif::error(T_("Invalid user id"));
			return false;
		}


		if($args['mobile'])
		{
			$check_mobile_exist = \dash\db\users::get_by_mobile($args['mobile']);
			if(isset($check_mobile_exist['id']) && intval($check_mobile_exist['id']) !== intval($id))
			{
				\dash\notif::error(T_("Duplicate mobile"), 'mobile');
				return false;
			}
		}

		// $user_id = self::find_user_id($args, $_option, true, $id);

		// if(intval($user_id) !== intval($id))
		// {
		// 	\dash\temp::set('app_user_id_changed', true);
		// 	\dash\temp::set('app_new_user_id_changed', $user_id);
		// 	\dash\temp::set('app_old_user_id_changed', $id);

		// 	$update_contact_user_id            = [];
		// 	$update_contact_user_id['user_id'] = $id;

		// 	if($_option['other_field'] && $_option['other_field_id'])
		// 	{
		// 		$update_contact_user_id[$_option['other_field']] = $_option['other_field_id'];
		// 	}

		// 	\dash\db\contacts::update_where(['user_id' => $user_id], $update_contact_user_id);
		// }

		// $_option['user_id']        = $user_id;

		// \dash\app\contact::merge($_args, $_option);

		\dash\db\users::update($args, $id);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("User successfully updated"));
		}
	}
}
?>