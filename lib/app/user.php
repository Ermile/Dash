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
			if($_option['save_log']) \dash\app::log('addon:api:user:permission:max:lenght', \dash\user::id(), $log_meta);
			if($_option['debug']) \dash\notif::error(T_("Permission is incorrect"), 'permission');
			return false;
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
		if($password)
		{
			$args['password'] = \dash\utility::hasher($password);
		}


		$args['mobile']       = $mobile;
		$args['displayname']  = $displayname;
		$args['title']        = $title;
		$args['avatar']       = $avatar;
		$args['status']       = $status;
		$args['gender']       = $gender;
		$args['type']         = $type;
		$args['email']        = $email;
		$args['parent']       = $parent;
		$args['permission']   = $permission;
		$args['username']     = $username;
		$args['pin']          = $pin;
		$args['ref']          = $ref;
		$args['twostep']      = $twostep;
		$args['unit_id']      = $unit_id;
		$args['language']     = $language;

		return $args;
	}


	/**
	 * ready data of user to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
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
					$result['avatar'] = $value ? $value : \dash\app::static_avatar_url();
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		$result['fullname'] = '';

		if(isset($result['firstname']))
		{
			$result['fullname'] .= $result['firstname'];
		}

		if(isset($result['lastname']))
		{
			$result['fullname'] .= ' '. $result['lastname'];
		}

		return $result;
	}

}
?>