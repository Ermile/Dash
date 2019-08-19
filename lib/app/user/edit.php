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
	public static function edit($_args, $_id, $_option = [])
	{
		\dash\app::variable($_args, ['raw_field' => ['signature']]);

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


		$id = $_id;
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

		// check args
		$args = self::check($id, $_option, $load_user);

		if($args === false || !\dash\engine\process::status())
		{
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

		if($args['email'])
		{
			$check_email_exist = \dash\db\users::get(['email' => $args['email'], 'limit' => 1]);
			if(isset($check_email_exist['id']) && intval($check_email_exist['id']) !== intval($id))
			{
				\dash\notif::error(T_("Duplicate email"), 'email');
				return false;
			}
		}


		if(\dash\app::isset_request('nationalcode') || \dash\app::isset_request('pasportcode'))
		{
			if($args['nationalcode'] || $args['pasportcode'])
			{
				$check_duplicate_nationalcode = self::check_duplicate($args['nationalcode'], $args['pasportcode']);

				if(isset($check_duplicate_nationalcode['id']) && intval($check_duplicate_nationalcode['id']) === intval($id))
				{
					// no problem to edit yourself
				}
				elseif($check_duplicate_nationalcode)
				{
					if($args['nationalcode'])
					{
						$nationalcode_q = $args['nationalcode'];
					}
					else
					{
						$nationalcode_q = $args['pasportcode'];
					}

					$msg = T_("Duplicate nationalcode or pasportcode in your user list");
					$msg = "<a href='". \dash\url::kingdom(). '/crm/member?q='. $nationalcode_q. "'>$msg</a>";
					\dash\notif::error($msg, ['nationalcode', 'pasportcode']);
					return false;

				}
			}
		}


		if(!\dash\app::isset_request('mobile'))     	unset($args['mobile']);
		if(!\dash\app::isset_request('signature'))     	unset($args['signature']);
		if(!\dash\app::isset_request('displayname')) 	unset($args['displayname']);
		if(!\dash\app::isset_request('title'))      	unset($args['title']);
		if(!\dash\app::isset_request('avatar'))     	unset($args['avatar']);
		if(!\dash\app::isset_request('status'))     	unset($args['status']);
		if(!\dash\app::isset_request('gender'))     	unset($args['gender']);
		if(!\dash\app::isset_request('type'))       	unset($args['type']);
		if(!\dash\app::isset_request('email'))      	unset($args['email']);
		if(!\dash\app::isset_request('parent'))     	unset($args['parent']);
		if(!\dash\app::isset_request('permission')) 	unset($args['permission']);
		if(!\dash\app::isset_request('username'))   	unset($args['username']);
		if(!\dash\app::isset_request('pin'))        	unset($args['pin']);
		if(!\dash\app::isset_request('ref'))        	unset($args['ref']);
		if(!\dash\app::isset_request('twostep'))    	unset($args['twostep']);
		if(!\dash\app::isset_request('forceremember'))  unset($args['forceremember']);
		if(!\dash\app::isset_request('unit_id'))    	unset($args['unit_id']);
		if(!\dash\app::isset_request('language'))   	unset($args['language']);
		if(!\dash\app::isset_request('password'))   	unset($args['password']);
		if(!\dash\app::isset_request('website'))    	unset($args['website']);
		if(!\dash\app::isset_request('facebook'))   	unset($args['facebook']);
		if(!\dash\app::isset_request('twitter'))    	unset($args['twitter']);
		if(!\dash\app::isset_request('instagram'))  	unset($args['instagram']);
		if(!\dash\app::isset_request('linkedin'))   	unset($args['linkedin']);
		if(!\dash\app::isset_request('gmail'))      	unset($args['gmail']);
		if(!\dash\app::isset_request('sidebar'))    	unset($args['sidebar']);
		if(!\dash\app::isset_request('firstname'))  	unset($args['firstname']);
		if(!\dash\app::isset_request('lastname'))   	unset($args['lastname']);
		if(!\dash\app::isset_request('bio'))        	unset($args['bio']);
		if(!\dash\app::isset_request('birthday'))   	unset($args['birthday']);
		if(!\dash\app::isset_request('theme'))        	unset($args['theme']);
		// if(!\dash\app::isset_request('tgstatus'))   	unset($args['tgstatus']);
		// if(!\dash\app::isset_request('tgusername'))   	unset($args['tgusername']);
		if(!\dash\app::isset_request('father'))         unset($args['father']);
		if(!\dash\app::isset_request('nationalcode'))   unset($args['nationalcode']);
		if(!\dash\app::isset_request('marital'))        unset($args['marital']);
		if(!\dash\app::isset_request('pasportcode'))    unset($args['pasportcode']);
		if(!\dash\app::isset_request('pasportdate'))    unset($args['pasportdate']);
		if(!\dash\app::isset_request('phone'))          unset($args['phone']);
		if(!\dash\app::isset_request('foreign'))        unset($args['foreign']);
		if(!\dash\app::isset_request('nationality'))    unset($args['nationality']);


		if(!empty($args))
		{
			\dash\log::set('editUser', ['code' => $id, 'datalink' => \dash\coding::encode($id)]);
			\dash\db\users::update($args, $id);
		}

		if(\dash\engine\process::status())
		{
			if(intval($id) === intval(\dash\user::id()))
			{
				\dash\user::refresh();
			}

			\dash\notif::ok(T_("User successfully updated"));
		}
	}
}
?>