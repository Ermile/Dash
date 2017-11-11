<?php
namespace lib\app;
use \lib\utility;
use \lib\debug;

/**
 * Class for user.
 */
class user
{

	use \lib\app\user\add;
	use \lib\app\user\edit;
	use \lib\app\user\datalist;
	use \lib\app\user\get;
	use \lib\app\user\user_id;


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

		// get mobile
		$mobile = \lib\app::request("mobile");
		$mobile = trim($mobile);
		if($mobile && !($mobile = \lib\utility\filter::mobile($mobile)))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:mobile:invalid', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Mobile is incorrect"), 'mobile');
			return false;
		}

		// get displayname
		$displayname = \lib\app::request("displayname");
		$displayname = trim($displayname);
		if($displayname && mb_strlen($displayname) > 50)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:displayname:max:length', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("You can set the displayname less than 50 character"), 'displayname');
			return false;
		}

		// get title
		$title = \lib\app::request("title");
		$title = trim($title);
		if($title && mb_strlen($title) > 50)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:title:max:length', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("You can set the title less than 50 character"), 'title');
			return false;
		}

		// get avatar
		$avatar = \lib\app::request('avatar');
		$avatar = trim($avatar);
		if($avatar && !is_string($avatar))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:avatar:not:string', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Invalid parameter avatar"), 'avatar');
			return false;
		}

		// get status
		$status = \lib\app::request('status');
		if($status && !in_array($status, ['active','awaiting','deactive','removed','filter','unreachable']))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:status:invalid', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Invalid parameter status"), 'status');
			return false;
		}


		$gender = \lib\app::request('gender');
		if($gender && !in_array($gender, ['male', 'female']))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:gender:invalid', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Invalid gender field"), 'gender');
			return false;
		}

		$type = \lib\app::request('type');
		$type = trim($type);
		if($type && mb_strlen($type) > 50)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:type:max:length', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("You must set the type less than 50 character"), 'type');
			return false;
		}

		// we never get password password
		// the password only get in enter

		$email = \lib\app::request('email');
		$email = trim($email);
		if($email && mb_strlen($email) > 50)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:email:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Email is incorrect"), 'email');
			return false;
		}

		$parent = \lib\app::request('parent');
		$parent = utility\shortURL::decode($parent);
		if(!$parent && \lib\app::request('parent'))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:parent:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Parent is incorrect"), 'parent');
			return false;
		}

		$permission = \lib\app::request('permission');
		$permission = trim($permission);
		if($permission && mb_strlen($permission) >= 1000)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:permission:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Permission is incorrect"), 'permission');
			return false;
		}

		$username = \lib\app::request('username');
		$username = trim($username);
		if($username && mb_strlen($username) > 50)
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:username:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Username is incorrect"), 'username');
			return false;
		}

		$pin = \lib\app::request('pin');
		if(($pin && mb_strlen($pin) > 4) || ($pin && !is_numeric($pin)))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:pin:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Pin is incorrect"), 'pin');
			return false;
		}

		$ref = \lib\app::request('ref');
		$ref = utility\shortURL::decode($ref);
		if(!$ref && \lib\app::request('ref'))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:ref:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Ref is incorrect"), 'ref');
			return false;
		}

		$twostep = null;
		if(utility::isset_request('twostep'))
		{
			$twostep = \lib\app::request('twostep');
			$twostep = $twostep ? 1 : 0;
		}

		$unit_id = \lib\app::request('unit_id');
		if($unit_id && !is_numeric($unit_id))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:unit_id:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Unit id is incorrect"), 'unit_id');
			return false;
		}

		$language = \lib\app::request('language');
		if($language && !\lib\utility\location\language::check($language))
		{
			if($_option['save_log']) \lib\app::log('addon:api:user:language:max:lenght', \lib\user::id(), $log_meta);
			if($_option['debug']) debug::error(T_("Language is incorrect"), 'language');
			return false;
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
						$result[$key] = \lib\utility\shortURL::encode($value);
					}
					else
					{
						$result[$key] = null;
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