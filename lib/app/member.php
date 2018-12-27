<?php
namespace dash\app;

/**
 * Class for member.
 */
class member
{

	public static function add($_args = [])
	{
		\dash\app::variable($_args);

		if(!\dash\user::id())
		{
			\dash\notif::error(T_("User id not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return = [];


		if(!$args['status'])
		{
			$args['status']  = 'awaiting';
		}

		if(\dash\app::isset_request('nationalcode') || \dash\app::isset_request('pasportcode'))
		{
			if($args['nationalcode'] || $args['pasportcode'])
			{
				$check_duplicate_nationalcode = self::check_duplicate($args['nationalcode'], $args['pasportcode']);

				if($check_duplicate_nationalcode)
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

		if(\dash\app::isset_request('mobile'))
		{
			$mobile = \dash\utility\filter::mobile(\dash\app::request('mobile'));
			if($mobile)
			{
				$check = \dash\db\users::get_by_mobile($mobile);
				if($check)
				{
					\dash\notif::error(T_("Duplicate mobile"), 'mobile');
					return false;
				}
			}
		}

		$user_id = \dash\db\users::insert($args);
		$user_id = \dash\db::insert_id();

		if(!$user_id)
		{
			\dash\app::log('ErrorInInsertUser');
			\dash\notif::error(T_("No way to insert data"), 'db', 'system');
			return false;
		}

		\dash\log::set('CRMaddNewMember', ['code' => $user_id]);

		$return['member_id'] = \dash\coding::encode($user_id);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("User successfuly added"));
		}

		return $return;
	}



	public static function check_duplicate($_national_code, $_passport_code)
	{
		$result = false;
		if($_national_code)
		{
			// check not duplicate nationalcode only
			$result = \dash\db\users::get(['nationalcode' => "$_national_code",  'limit' => 1]);
		}
		else
		{
			// check pasportcode only
			$result = \dash\db\users::get(['pasportcode' => "$_passport_code", 'limit' => 1]);
		}

		return $result;

	}


	public static $sort_field =
	[
		'id',
		'gender',
		'firstname',
		'lastname',
		'father',
		'birthdate',
		'nationalcode',
		'pasportcode',
		'mobile',
		'type',
		'permission',
		'code',
	];


	public static function list($_string = null, $_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}

		$default_meta =
		[
			'sort'              => null,
			'order'             => null,
			'pagenation'        => true,
		];


		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_meta, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}


		$result            = \dash\db\users::search($_string, $_args);


		if(!is_array($result) && isset($_args['get_count']))
		{
			return $result;
		}

		$temp              = [];

		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}

	/**
	 * edit a member
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function edit($_args, $_id)
	{
		\dash\app::variable($_args);

		$result = self::get($_id);

		if(!$result)
		{
			return false;
		}

		$id = \dash\coding::decode($_id);

		$args = self::check($id);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
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

		if(\dash\app::isset_request('mobile'))
		{
			$mobile = \dash\utility\filter::mobile(\dash\app::request('mobile'));
			if($mobile)
			{
				$check = \dash\db\users::get_by_mobile($mobile);
				if($check)
				{
					if(isset($check['id']) && intval($check['id']) === intval($id))
					{
						// no problem
					}
					else
					{
						\dash\notif::error(T_("Duplicate mobile"), 'mobile');
						return false;
					}
				}
			}
		}



		$load_detail = \dash\db\users::get(['id' => $id, 'limit' => 1]);

		// cat not delete supervisor permission
		if(isset($load_detail['permission']) && $load_detail['permission'] === 'supervisor')
		{
			unset($args['permission']);
		}


		if(!\dash\app::isset_request('mobile'))         unset($args['mobile']);
		if(!\dash\app::isset_request('email'))          unset($args['email']);
		// if(!\dash\app::isset_request('shfrom'))      unset($args['shfrom']);
		if(!\dash\app::isset_request('firstname'))      unset($args['firstname']);
		if(!\dash\app::isset_request('lastname'))       unset($args['lastname']);
		if(!\dash\app::isset_request('father'))         unset($args['father']);
		if(!\dash\app::isset_request('nationalcode'))   unset($args['nationalcode']);
		if(!\dash\app::isset_request('pasportcode'))    unset($args['pasportcode']);
		if(!\dash\app::isset_request('birthdate'))      unset($args['birthday']);
		if(!\dash\app::isset_request('pasportdate'))    unset($args['pasportdate']);
		if(!\dash\app::isset_request('gender'))         unset($args['gender']);
		if(!\dash\app::isset_request('marital'))        unset($args['marital']);
		if(!\dash\app::isset_request('avatar'))         unset($args['avatar']);
		if(!\dash\app::isset_request('phone'))          unset($args['phone']);
		if(!\dash\app::isset_request('status'))         unset($args['status']);
		if(!\dash\app::isset_request('desc'))           unset($args['desc']);
		if(!\dash\app::isset_request('foreign'))        unset($args['foreign']);
		if(!\dash\app::isset_request('nationality'))    unset($args['nationality']);
		if(!\dash\app::isset_request('website')) 		unset($args['website']);
		if(!\dash\app::isset_request('instagram')) 		unset($args['instagram']);
		if(!\dash\app::isset_request('linkedin')) 		unset($args['linkedin']);
		if(!\dash\app::isset_request('facebook')) 		unset($args['facebook']);
		if(!\dash\app::isset_request('twitter')) 		unset($args['twitter']);

		if(!\dash\app::isset_request('twostep')) 		unset($args['twostep']);
		if(!\dash\app::isset_request('forceremember')) 	unset($args['forceremember']);
		if(!\dash\app::isset_request('title')) 			unset($args['title']);
		if(!\dash\app::isset_request('bio')) 			unset($args['bio']);
		if(!\dash\app::isset_request('displayname')) 	unset($args['displayname']);
		if(!\dash\app::isset_request('language')) 		unset($args['language']);



		// if(!\dash\app::isset_request('shcode'))         unset($args['shcode']);
		// if(!\dash\app::isset_request('birthcity'))      unset($args['birthcity']);
		// if(!\dash\app::isset_request('zipcode'))        unset($args['zipcode']);
		// if(!\dash\app::isset_request('religion'))       unset($args['religion']);
		// if(!\dash\app::isset_request('education'))      unset($args['education']);
		// if(!\dash\app::isset_request('education2'))     unset($args['education2']);
		// if(!\dash\app::isset_request('educationcourse')) unset($args['educationcourse']);
		// if(!\dash\app::isset_request('city'))           unset($args['city']);
		// if(!\dash\app::isset_request('province'))       unset($args['province']);
		// if(!\dash\app::isset_request('country'))        unset($args['country']);
		// if(!\dash\app::isset_request('address'))        unset($args['address']);
		// if(!\dash\app::isset_request('mobile2'))        unset($args['mobile2']);
		// if(!\dash\app::isset_request('fathermobile'))   unset($args['fathermobile']);
		// if(!\dash\app::isset_request('mothermobile'))   unset($args['mothermobile']);


		if(!empty($args))
		{
			$update = \dash\db\users::update($args, $id);
			\dash\log::set('CRMeditMember', ['code' => $id]);

			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("User successfully updated"));
			}

			return $update;
		}
	}

	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			\dash\notif::error(T_("user id not set"));
			return false;
		}

		$get = \dash\db\users::get(['id' => $id, 'limit' => 1]);

		if(!$get)
		{
			\dash\notif::error(T_("Invalid user id"));
			return false;
		}

		$result = self::ready($get);

		return $result;
	}


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	private static function check($_id = null)
	{
		$args                    = [];

		// if the force_add is true
		// we not check some of requirement arguments
		// just the supervisor can set this arguments
		$force_add = false;
		if(\dash\app::request('force_add'))
		{
			$force_add = true;
		}

		$mobile = \dash\app::request('mobile');
		if(\dash\app::isset_request('mobile'))
		{
			if(!$mobile && !$force_add)
			{
				\dash\notif::error(T_("Mobile is required"), 'mobile');
				return false;
			}
		}

		if($mobile && !\dash\utility\filter::mobile($mobile))
		{
			\dash\notif::error(T_("Invalid mobile"), 'mobile');
			return false;
		}

		if($mobile)
		{
			$mobile = \dash\utility\filter::mobile($mobile);
		}

		$firstname = \dash\app::request('firstname');
		if($firstname && mb_strlen($firstname) > 100)
		{
			\dash\notif::error(T_("Plese set firstname less than 100 character"), 'firstname');
			return false;
		}

		$lastname = \dash\app::request('lastname');
		if($lastname && mb_strlen($lastname) > 100)
		{
			\dash\notif::error(T_("Plese set firstname less than 100 character"), 'lastname');
			return false;
		}

		if(\dash\app::isset_request('firstname') || \dash\app::isset_request('lastname'))
		{
			if(!$firstname && !$lastname && !$force_add)
			{
				// \dash\notif::error(T_("Firstname or lastname is required"), 'firstname');
				// return false;
			}
		}

		$father = \dash\app::request('father');

		if($father && mb_strlen($father) > 100)
		{
			\dash\notif::error(T_("Invalid father"), 'father');
			return false;
		}

		$nationality = \dash\app::request('nationality');
		if($nationality && !\dash\utility\location\countres::check($nationality))
		{
			\dash\notif::error(T_("Invalid nationality"), 'nationality');
			return false;
		}

		$nationalcode = \dash\app::request('nationalcode');
		if($nationalcode && !\dash\utility\filter::nationalcode($nationalcode))
		{
			\dash\notif::error(T_("Invalid nationalcode syntax"), 'nationalcode');
			return false;
		}


		$pasportcode = \dash\app::request('pasportcode');
		$pasportcode = mb_strtolower($pasportcode);
		$pasportcode = \dash\utility\convert::to_en_number($pasportcode);
		if($pasportcode && mb_strlen($pasportcode) > 30 )
		{
			\dash\notif::error(T_("Invalid pasportcode"), 'pasportcode');
			return false;
		}


		$birthdate = null;

		if(\dash\app::isset_request('birthdate'))
		{
			$birthdate = \dash\app::request('birthdate');
			if($birthdate)
			{
				$birthdate = \dash\date::db($birthdate);
				$birthdate = \dash\date::birthdate($birthdate, true);

				if(!$birthdate)
				{
					return false;
				}
			}
		}

		$pasportdate = \dash\app::request('pasportdate');
		$pasportdate = \dash\date::db($pasportdate);
		if($pasportdate === false)
		{
			\dash\notif::error(T_("Invalid pasportdate"), 'pasportdate');
			return false;
		}

		if($pasportdate)
		{
			if(\dash\utility\jdate::is_jalali($pasportdate))
			{
				$pasportdate = \dash\utility\jdate::to_gregorian($pasportdate);
			}
		}

		$gender = \dash\app::request('gender');
		if(\dash\app::isset_request('gender'))
		{
			// if(!$gender && !$force_add)
			// {
			// 	\dash\notif::error(T_('Gender is required'), 'gender');
			// 	return false;
			// }
		}

		$website = \dash\app::request('website');
		if($website && mb_strlen($website) > 100)
		{
			\dash\notif::error(T_("Please set website less than 100 character"), 'website');
			return false;
		}

		$instagram = \dash\app::request('instagram');
		if($instagram && mb_strlen($instagram) > 100)
		{
			\dash\notif::error(T_("Please set instagram less than 100 character"), 'instagram');
			return false;
		}

		$linkedin = \dash\app::request('linkedin');
		if($linkedin && mb_strlen($linkedin) > 100)
		{
			\dash\notif::error(T_("Please set linkedin less than 100 character"), 'linkedin');
			return false;
		}

		$facebook = \dash\app::request('facebook');
		if($facebook && mb_strlen($facebook) > 100)
		{
			\dash\notif::error(T_("Please set facebook less than 100 character"), 'facebook');
			return false;
		}

		$twitter = \dash\app::request('twitter');
		if($twitter && mb_strlen($twitter) > 100)
		{
			\dash\notif::error(T_("Please set twitter less than 100 character"), 'twitter');
			return false;
		}


		if($gender && !in_array($gender, ['male', 'female']))
		{
			\dash\notif::error(T_("Invalid gender"), 'gender');
			return false;
		}

		$marital = \dash\app::request('marital');
		if($marital && !in_array($marital, ['single', 'married']))
		{
			\dash\notif::error(T_("Invalid marital"), 'marital');
			return false;
		}

		$detail = [];

		if($_id)
		{
			$load = \dash\db\users::get_by_id($_id);
			if(isset($load['detail']))
			{
				$detail = json_decode($load['detail'], true);
			}
		}

		if(!$detail || !is_array($detail))
		{
			$detail = [];
		}

		$shcode = \dash\app::request('shcode');
		$shcode = \dash\utility\convert::to_en_number($shcode);
		if($shcode && !is_numeric($shcode))
		{
			\dash\notif::error(T_("Invalid shcode"), 'shcode');
			return false;
		}

		if($shcode && intval($shcode) > 1E+10)
		{
			\dash\notif::error(T_("Invalid shcode"), 'shcode');
			return false;
		}

		if(\dash\app::isset_request('shcode'))
		{
			$detail['shcode'] = $shcode;
		}


		$birthcity = \dash\app::request('birthcity');
		if($birthcity && mb_strlen($birthcity) > 50)
		{
			\dash\notif::error(T_("Invalid birthcity"), 'birthcity');
			return false;
		}

		if(\dash\app::isset_request('birthcity'))
		{
			$detail['birthcity'] = $birthcity;
		}


		$religion = \dash\app::request('religion');
		if($religion && mb_strlen($religion) > 50)
		{
			\dash\notif::error(T_("Invalid religion"), 'religion');
			return false;
		}

		if(\dash\app::isset_request('religion'))
		{
			$detail['religion'] = $religion;
		}


		$avatar = \dash\app::request('avatar');
		if($avatar && mb_strlen($avatar) > 2000)
		{
			\dash\notif::error(T_("Invalid avatar"), 'avatar');
			return false;
		}

		$education = \dash\app::request('education');
		if($education && mb_strlen($education) > 100)
		{
			\dash\notif::error(T_("Invalid education"), 'education');
			return false;
		}

		if(\dash\app::isset_request('education'))
		{
			$detail['education'] = $education;
		}


		$educationcourse = \dash\app::request('educationcourse');
		if($educationcourse && mb_strlen($educationcourse) > 100)
		{
			\dash\notif::error(T_("Invalid educationcourse"), 'educationcourse');
			return false;
		}

		if(\dash\app::isset_request('educationcourse'))
		{
			$detail['educationcourse'] = $educationcourse;
		}

		$shfrom = \dash\app::request('shfrom');
		if ($shfrom && mb_strlen($shfrom) > 200)
		{
			\dash\notif::error(T_("Invalid issue place"), 'shfrom');
			return false;
		}

		if(\dash\app::isset_request('shfrom'))
		{
			$detail['shfrom'] = $shfrom;
		}

		if(\dash\app::isset_request('file1'))
		{
			$detail['file1'] = \dash\app::request('file1');
		}

		if(\dash\app::isset_request('file2'))
		{
			$detail['file2'] = \dash\app::request('file2');
		}


		$email = \dash\app::request('email');
		if ($email && mb_strlen($email) > 150)
		{
			\dash\notif::error(T_("Invalid email"), 'email');
			return false;
		}

		if(\dash\app::isset_request('email'))
		{
			$detail['email'] = $email;
		}

		$phone = \dash\app::request('phone');
		if($phone && mb_strlen($phone) > 50)
		{
			\dash\notif::error(T_("Invalid phone"), 'phone');
			return false;
		}

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['active','awaiting','deactive','removed','filter','unreachable']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}

		$desc = \dash\app::request('desc');

		if(\dash\app::isset_request('permission'))
		{
			$permission = \dash\app::request('permission');
			if(\dash\permission::check("aMemberPermissionChange"))
			{
				if($permission && !in_array($permission, array_keys(\dash\permission::groups())))
				{
					if($permission === 'supervisor')
					{
						if(!\dash\permission::supervisor())
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
						\dash\notif::error(T_("Permission is incorrect"), 'permission');
						return false;
					}
				}
			}

			$args['permission']          = $permission;
		}


		if(!empty($detail))
		{
			$args['detail'] = json_encode($detail, JSON_UNESCAPED_UNICODE);
		}

		$twostep       = \dash\app::request('twostep') ? 1 : null;
		$forceremember = \dash\app::request('forceremember') ? 1 : null;

		$password = \dash\app::request('password');

		if(\dash\permission::check("cpUsersPasswordChange"))
		{
			if($password)
			{
				if(mb_strlen($password) < 6)
				{
					\dash\notif::error(T_("Plase set password larger than 6 character"), ['element' => ['password', 'repassword']]);
					return false;
				}

				$args['password'] = \dash\utility::hasher($password, null, false);
				if(!\dash\engine\process::status())
				{
					return false;
				}
			}
		}

		$title = \dash\app::request('title');
		if($title && mb_strlen($title) > 100)
		{
			\dash\notif::error(T_("Plase set title less than 100 character"), 'title');
			return false;
		}

		$bio = \dash\app::request('bio');
		if($bio && mb_strlen($bio) > 100)
		{
			\dash\notif::error(T_("Plase set bio less than 100 character"), 'bio');
			return false;
		}

		$displayname = \dash\app::request('displayname');
		if($displayname && mb_strlen($displayname) > 100)
		{
			\dash\notif::error(T_("Plase set displayname less than 100 character"), 'displayname');
			return false;
		}


		$language = \dash\app::request('language');
		if($language && !\dash\language::check($language))
		{
			\dash\notif::error(T_("Language is incorrect"), 'language');
			return false;
		}

		$username = \dash\app::request('username');
		if($username)
		{
			if(mb_strlen($username) < 4)
			{
				\dash\notif::error(T_("Please set the username larger than 4 character"), 'username');
				return false;
			}

			if(mb_strlen($username) > 50)
			{
				\dash\notif::error(T_("Please set the username less than 50 character"), 'username');
				return false;
			}

			if($username && !preg_match("/^[A-Za-z0-9]+$/", $username))
			{
				\dash\notif::error(T_("Only [A-Za-z0-9] can use in username"), 'username');
				return false;
			}

			$check_duplicate_username = \dash\db\users::get(['username' => $username, 'limit' => 1]);
			if(isset($check_duplicate_username['id']))
			{
				if(intval($check_duplicate_username['id']) === intval($_id))
				{

				}
				else
				{
					\dash\notif::error(T_("Duplicate username"), 'username');
					return false;
				}
				$args['username'] = $username;
			}
		}

		if(\dash\app::isset_request('username') && !$username)
		{
			$args['username'] = null;
		}


		$args['username']      = $username;
		$args['language']      = $language;
		$args['title']         = $title;
		$args['bio']           = $bio;
		$args['displayname']   = $displayname;
		$args['mobile']        = $mobile;
		$args['nationalcode']  = $nationalcode;
		$args['pasportcode']   = $pasportcode;
		$args['firstname']     = $firstname;
		$args['lastname']      = $lastname;
		$args['father']        = $father;
		$args['birthday']      = $birthdate;
		$args['pasportdate']   = $pasportdate;
		$args['gender']        = $gender;
		$args['marital']       = $marital;
		$args['avatar']        = $avatar;
		$args['nationality']   = $nationality;
		$args['phone']         = $phone;
		$args['status']        = $status;
		$args['desc']          = $desc;
		$args['website']       = $website;
		$args['instagram']     = $instagram;
		$args['linkedin']      = $linkedin;
		$args['facebook']      = $facebook;
		$args['twitter']       = $twitter;
		$args['twostep']       = $twostep;
		$args['forceremember'] = $forceremember;
		return $args;
	}


	/**
	 * ready data of member to load in api
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
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;


				case 'permission':
					if($value)
					{
						$pGroup = \dash\permission::groups();
						if(isset($pGroup[$value]['title']))
						{
							$result[$key]               = $value;
							$result['permission_title'] = $pGroup[$value]['title'];
						}
						else
						{
							$result[$key] = $value;
						}
					}
					else
					{
						$result[$key] = null;
					}
				break;
				case 'detail':
					if($value)
					{
						$result[$key] = json_decode($value, true);
					}
					else
					{
						$result[$key] = $value;
					}
					break;
				case 'avatar':
					if($value)
					{
						$avatar = $value;
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
					}
					$result[$key] = $avatar;
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