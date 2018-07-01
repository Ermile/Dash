<?php
namespace dash\app;


class user
{

	use \dash\app\user\add;
	use \dash\app\user\edit;
	use \dash\app\user\datalist;
	use \dash\app\user\get;
	use \dash\app\user\user_id;


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function check($_option = [])
	{
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
			'save_log' => true,
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		// get mobile
		$mobile = \dash\app::request("mobile");
		$mobile = trim($mobile);
		if($mobile && !($mobile = \dash\utility\filter::mobile($mobile)))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:mobile:invalid', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Mobile is incorrect"), 'mobile');
			return false;
		}

		// get displayname
		$displayname = \dash\app::request("displayname");
		$displayname = trim($displayname);
		if($displayname && mb_strlen($displayname) > 50)
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:displayname:max:length', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("You can set the displayname less than 50 character"), 'displayname');
			return false;
		}

		// get title
		$title = \dash\app::request("title");
		$title = trim($title);
		if($title && mb_strlen($title) > 50)
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:title:max:length', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("You can set the title less than 50 character"), 'title');
			return false;
		}

		// get avatar
		$avatar = \dash\app::request('avatar');
		$avatar = trim($avatar);
		if($avatar && !is_string($avatar))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:avatar:not:string', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Invalid parameter avatar"), 'avatar');
			return false;
		}

		// get status
		$status = \dash\app::request('status');
		if($status && !in_array($status, ['active','awaiting','deactive','removed','filter','unreachable']))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:status:invalid', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Invalid parameter status"), 'status');
			return false;
		}


		$gender = \dash\app::request('gender');
		if($gender && !in_array($gender, ['male', 'female']))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:gender:invalid', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Invalid gender field"), 'gender');
			return false;
		}

		$type = \dash\app::request('type');
		$type = trim($type);
		if($type && mb_strlen($type) > 50)
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:type:max:length', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("You must set the type less than 50 character"), 'type');
			return false;
		}

		// we never get password password
		// the password only get in enter

		$email = \dash\app::request('email');
		$email = trim($email);
		if($email && mb_strlen($email) > 50)
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:email:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Email is incorrect"), 'email');
			return false;
		}

		$parent = \dash\app::request('parent');
		$parent = \dash\coding::decode($parent);
		if(!$parent && \dash\app::request('parent'))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:parent:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Parent is incorrect"), 'parent');
			return false;
		}

		$permission = \dash\app::request('permission');
		if($permission && !in_array($permission, array_keys(\dash\permission::groups())))
		{
			if($permission === 'supervisor')
			{
				if(!\dash\url::isLocal() && !\dash\permission::supervisor())
				{
					\dash\notif::error("Permission is incorrect", 'permission');
					return false;
				}
				else
				{
					// no problem
					// supervisor make a new supervisor
				}
			}
			else
			{
				if($_option['save_log']) \dash\app::log('addon:api:user:permission:max:lenght', \dash\user::id(), $log_meta);
				if($_option['debug']) \dash\notif::error(T_("Permission is incorrect"), 'permission');
				return false;
			}
		}

		$username = \dash\app::request('username');
		$username = trim($username);
		if($username && mb_strlen($username) > 50)
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:username:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Username is incorrect"), 'username');
			return false;
		}

		$pin = \dash\app::request('pin');
		if(($pin && mb_strlen($pin) > 4) || ($pin && !is_numeric($pin)))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:pin:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Pin is incorrect"), 'pin');
			return false;
		}

		$ref = \dash\app::request('ref');
		$ref = \dash\coding::decode($ref);
		if(!$ref && \dash\app::request('ref'))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:ref:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Ref is incorrect"), 'ref');
			return false;
		}

		$twostep = null;
		if(\dash\app::isset_request('twostep'))
		{
			$twostep = \dash\app::request('twostep');
			$twostep = $twostep ? 1 : 0;
		}

		$unit_id = \dash\app::request('unit_id');
		if($unit_id && !is_numeric($unit_id))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:unit_id:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Unit id is incorrect"), 'unit_id');
			return false;
		}

		$language = \dash\app::request('language');
		if($language && !\dash\language::check($language))
		{
			if($_option['save_log']) \dash\app::log('addon:api:user:language:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Language is incorrect"), 'language');
			return false;
		}

		$password = \dash\app::request('password');

		if(\dash\permission::check("cpUsersPasswordChange"))
		{
			if($password)
			{
				$args['password'] = \dash\utility::hasher($password);
			}
		}

		$website = \dash\app::request('website');
		$website = trim($website);
		if($website && mb_strlen($website) > 200)
		{
			\dash\notif::error(T_("website is out of range"), 'website');
			return false;
		}

		$facebook = \dash\app::request('facebook');
		$facebook = trim($facebook);
		if($facebook && mb_strlen($facebook) > 200)
		{
			\dash\notif::error(T_("facebook is out of range"), 'facebook');
			return false;
		}

		$twitter = \dash\app::request('twitter');
		$twitter = trim($twitter);
		if($twitter && mb_strlen($twitter) > 200)
		{
			\dash\notif::error(T_("twitter is out of range"), 'twitter');
			return false;
		}

		$instagram = \dash\app::request('instagram');
		$instagram = trim($instagram);
		if($instagram && mb_strlen($instagram) > 200)
		{
			\dash\notif::error(T_("instagram is out of range"), 'instagram');
			return false;
		}

		$linkedin = \dash\app::request('linkedin');
		$linkedin = trim($linkedin);
		if($linkedin && mb_strlen($linkedin) > 200)
		{
			\dash\notif::error(T_("linkedin is out of range"), 'linkedin');
			return false;
		}

		$gmail = \dash\app::request('gmail');
		$gmail = trim($gmail);
		if($gmail && mb_strlen($gmail) > 200)
		{
			\dash\notif::error(T_("gmail is out of range"), 'gmail');
			return false;
		}

		$sidebar = null;
		if(\dash\app::isset_request('sidebar'))
		{
			$sidebar = \dash\app::request('sidebar');
			$sidebar = trim($sidebar);
			$sidebar = $sidebar ? 1 : 0;
		}

		$firstname = \dash\app::request('firstname');
		$firstname = trim($firstname);
		if($firstname && mb_strlen($firstname) > 100)
		{
			\dash\notif::error(T_("firstname is out of range"), 'firstname');
			return false;
		}

		$lastname = \dash\app::request('lastname');
		$lastname = trim($lastname);
		if($lastname && mb_strlen($lastname) > 100)
		{
			\dash\notif::error(T_("lastname is out of range"), 'lastname');
			return false;
		}

		$bio = \dash\app::request('bio');
		$bio = trim($bio);
		if($bio && mb_strlen($bio) > 50000)
		{
			\dash\notif::error(T_("bio is out of range"), 'bio');
			return false;
		}

		$birthday = \dash\app::request('birthday');
		$birthday = \dash\date::birthdate($birthday, true);
		if($birthday === false)
		{
			return false;
		}

		$args['birthday']    = $birthday;
		$args['website']     = $website;
		$args['facebook']    = $facebook;
		$args['twitter']     = $twitter;
		$args['instagram']   = $instagram;
		$args['linkedin']    = $linkedin;
		$args['gmail']       = $gmail;
		$args['sidebar']     = $sidebar;
		$args['firstname']   = $firstname;
		$args['lastname']    = $lastname;
		$args['bio']         = $bio;

		$args['mobile']      = $mobile;
		$args['displayname'] = $displayname;
		$args['title']       = $title;
		$args['avatar']      = $avatar;
		$args['status']      = $status;
		$args['gender']      = $gender;
		$args['type']        = $type;
		$args['email']       = $email;
		$args['parent']      = $parent;
		$args['permission']  = $permission;
		$args['username']    = $username;
		$args['pin']         = $pin;
		$args['ref']         = $ref;
		$args['twostep']     = $twostep;
		$args['unit_id']     = $unit_id;
		$args['language']    = $language;

		if($args['permission'] === 'supervisor')
		{
			unset($args['permission']);
		}

		if(!\dash\permission::check("cpUsersPermission"))
		{
			unset($args['permission']);
		}

		return $args;
	}


	/**
	 * ready data of user to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data, $_id = null)
	{
		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'permission':
					if($value === 'supervisor' && !\dash\permission::supervisor())
					{
						return false;
					}
					else
					{
						$result[$key] = $value;
					}

					break;
				case 'id':
				case 'creator':
				case 'parent':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				case 'avatar':
					if($_id)
					{
						$result['avatar'] = $value;
					}
					else
					{
						if(isset($_data['gender']))
						{
							if($_data['gender'] === 'male')
							{
								$avatar = \dash\app::static_avatar_url('male');
							}
							else
							{
								$avatar = \dash\app::static_avatar_url('female');
							}
						}
						else
						{
							$avatar = \dash\app::static_avatar_url();
						}
						$result['avatar'] = $value ? $value : $avatar;
					}
					break;

				case 'sidebar':
					if($value || $value === null)
					{
						$result[$key] = true;
					}
					else
					{
						$result[$key] = false;
					}

					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}

}
?>