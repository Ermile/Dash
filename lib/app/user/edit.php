<?php
namespace lib\app\user;


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
		\lib\app::variable($_args);

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
				'input' => \lib\app::request(),
			]
		];

		// check args
		$args = self::check($_option);

		if($args === false || !\lib\notif::$status)
		{
			return false;
		}

		$id = \lib\app::request('id');
		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			\lib\app::log('api:staff:edit:permission:denide', \lib\user::id(), $log_meta);
			\lib\notif::error(T_("Can not access to edit staff"), 'staff');
			return false;
		}

		$user_id = self::find_user_id($args, $_option, true, $id);

		if(intval($user_id) !== intval($id))
		{
			\lib\temp::set('app_user_id_changed', true);
			\lib\temp::set('app_new_user_id_changed', $user_id);
			\lib\temp::set('app_old_user_id_changed', $id);

			$update_contact_user_id            = [];
			$update_contact_user_id['user_id'] = $id;

			if($_option['other_field'] && $_option['other_field_id'])
			{
				$update_contact_user_id[$_option['other_field']] = $_option['other_field_id'];
			}

			\lib\db\contacts::update_where(['user_id' => $user_id], $update_contact_user_id);
		}

		$_option['user_id']        = $user_id;

		\lib\app\contact::merge($_args, $_option);

		if(\lib\notif::$status)
		{
			\lib\notif::true(T_("Profile successfully updated"));
		}
	}
}
?>