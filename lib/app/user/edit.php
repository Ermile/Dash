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


		if(!\dash\app::isset_request('mobile'))     unset($args['mobile']);
		if(!\dash\app::isset_request('displayname')) unset($args['displayname']);
		if(!\dash\app::isset_request('title'))      unset($args['title']);
		if(!\dash\app::isset_request('avatar'))     unset($args['avatar']);
		if(!\dash\app::isset_request('status'))     unset($args['status']);
		if(!\dash\app::isset_request('gender'))     unset($args['gender']);
		if(!\dash\app::isset_request('type'))       unset($args['type']);
		if(!\dash\app::isset_request('email'))      unset($args['email']);
		if(!\dash\app::isset_request('parent'))     unset($args['parent']);
		if(!\dash\app::isset_request('permission')) unset($args['permission']);
		if(!\dash\app::isset_request('username'))   unset($args['username']);
		if(!\dash\app::isset_request('pin'))        unset($args['pin']);
		if(!\dash\app::isset_request('ref'))        unset($args['ref']);
		if(!\dash\app::isset_request('twostep'))    unset($args['twostep']);
		if(!\dash\app::isset_request('unit_id'))    unset($args['unit_id']);
		if(!\dash\app::isset_request('language'))   unset($args['language']);
		if(!\dash\app::isset_request('password'))   unset($args['password']);
		if(!\dash\app::isset_request('website'))    unset($args['website']);
		if(!\dash\app::isset_request('facebook'))   unset($args['facebook']);
		if(!\dash\app::isset_request('twitter'))    unset($args['twitter']);
		if(!\dash\app::isset_request('instagram'))  unset($args['instagram']);
		if(!\dash\app::isset_request('linkedin'))   unset($args['linkedin']);
		if(!\dash\app::isset_request('gmail'))      unset($args['gmail']);
		if(!\dash\app::isset_request('sidebar'))    unset($args['sidebar']);
		if(!\dash\app::isset_request('firstname'))  unset($args['firstname']);
		if(!\dash\app::isset_request('lastname'))   unset($args['lastname']);
		if(!\dash\app::isset_request('bio'))        unset($args['bio']);
		if(!\dash\app::isset_request('birthday'))   unset($args['birthday']);

		if(!empty($args))
		{
			\dash\db\users::update($args, $id);
		}

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("User successfully updated"));
		}
	}
}
?>