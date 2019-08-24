<?php
namespace dash\app;


class user
{

	use \dash\app\user\add;
	use \dash\app\user\edit;
	use \dash\app\user\datalist;
	use \dash\app\user\get;
	use \dash\app\user\user_id;


	public static function lates_user($_args = [])
	{
		if(!isset($_args['limit']))
		{
			$_args['limit'] = 5;
		}

		$_args['order_raw'] = 'users.id DESC';
		$_args['pagenation'] = false;

		$list = \dash\db\users::search(null, $_args);

		if(is_array($list))
		{
			$list = array_map(['\\dash\\app\\user', 'ready'], $list);
		}

		return $list;
	}

	public static function chart_gender()
	{
		$result     = \dash\db\users::get_gender_chart();
		$hi_chart   = [];

		if(!is_array($result))
		{
			$result = [];
		}

		foreach ($result as $key => $value)
		{
			$name  = null;
			$count = 0;

			if(is_array($value) && array_key_exists('gender', $value))
			{
				$name = $value['gender'] ? T_($value['gender']) : T_("Unknown");
			}

			if(is_array($value) && array_key_exists('count', $value))
			{
				$count = intval($value['count']);
			}
			$newValue = ['name' => $name, 'y' => $count];
			if(is_array($value) && array_key_exists('gender', $value) && !$value['gender'])
			{
				$newValue['visible'] = false;
			}
			$hi_chart[] = $newValue;
		}

		$hi_chart = json_encode($hi_chart, JSON_UNESCAPED_UNICODE);

		return $hi_chart;

	}

	public static function chart_status()
	{
		$result = \dash\db\users::get_status_chart();
			$hi_chart   = [];

		if(!is_array($result))
		{
			$result = [];
		}

		foreach ($result as $key => $value)
		{
			$name  = null;
			$count = 0;

			if(array_key_exists('status', $value))
			{
				$name = $value['status'] ? T_($value['status']) : T_("Unknown");
			}

			if(array_key_exists('count', $value))
			{
				$count = intval($value['count']);
			}

			$hi_chart[] = ['name' => $name, 'y' => $count];
		}

		$hi_chart      = json_encode($hi_chart, JSON_UNESCAPED_UNICODE);

		return $hi_chart;


	}


	public static function chart_identify($_args = [], $_get_raw = false)
	{

		$result = \dash\db\users::get_identify_chart();

		$hi_chart   = [];
		$categories = [];
		$values     = [];
		$raw        = [];

		if(!is_array($result))
		{
			$result = [];
		}

		$all = \dash\db\users::get_count();
		$all = intval($all);
		if($all === 0)
		{
			$all = 1;
		}


		foreach ($result as $key => $value)
		{
			$temp     = 0;
			$type     = null;
			$type_raw = null;

			if(array_key_exists('type', $value))
			{
				$type = $value['type'] ? T_($value['type']) : T_("Unknown");
				$categories[] = $type;
				$type_raw = $value['type'];
			}

			if(array_key_exists('count', $value))
			{
				$temp = intval($value['count']);
				$values[] = intval($temp);
			}

			$raw[$type_raw] = round(($temp * 100) / $all, 1);
		}

		$hi_chart['categories'] = json_encode($categories, JSON_UNESCAPED_UNICODE);
		$hi_chart['value']      = json_encode($values, JSON_UNESCAPED_UNICODE);

		$return                 = [];
		$return['chart']        = $hi_chart;
		$return['raw']          = $raw;
		return $return;

	}





	public static function get_full_name($_user_id, $_get_gender = false)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		$load = \dash\db\users::get(['id' => $_user_id, 'limit' => 1]);
		$name = null;
		if(isset($load['firstname']) && isset($load['lastname']))
		{
			$name = trim($load['firstname']. ' '. $load['lastname']);
		}
		elseif(isset($load['displayname']))
		{
			$name = $load['displayname'];
		}

		if($_get_gender)
		{
			if(isset($load['gender']))
			{
				if($load['gender'] === 'male')
				{
					$name = T_("Mr"). ' '. $name;
				}
				elseif($load['gender'] === 'female')
				{
					$name = T_("Mrs"). ' '. $name;
				}
			}
		}

		return $name;
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

	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function check($_id = null, $_option = [], $_load_user = [])
	{
		$args                    = [];

		$default_option =
		[
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$debug = $_option['debug'];


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
				// in crm content the mobile is not required
				if(\dash\url::content() !== 'crm')
				{
					if($debug) \dash\notif::error(T_("Mobile is required"), 'mobile');
					return false;
				}
			}
		}

		if($mobile && !\dash\utility\filter::mobile($mobile))
		{
			if($debug) \dash\notif::error(T_("Invalid mobile"), 'mobile');
			return false;
		}

		if($mobile)
		{
			$mobile = \dash\utility\filter::mobile($mobile);
		}

		$firstname = \dash\app::request('firstname');
		if($firstname && mb_strlen($firstname) > 100)
		{
			if($debug) \dash\notif::error(T_("Plese set firstname less than 100 character"), 'firstname');
			return false;
		}

		$lastname = \dash\app::request('lastname');
		if($lastname && mb_strlen($lastname) > 100)
		{
			if($debug) \dash\notif::error(T_("Plese set firstname less than 100 character"), 'lastname');
			return false;
		}

		$father = \dash\app::request('father');
		if($father && mb_strlen($father) > 100)
		{
			if($debug) \dash\notif::error(T_("Invalid father"), 'father');
			return false;
		}

		$nationality = \dash\app::request('nationality');
		if($nationality && !\dash\utility\location\countres::check($nationality))
		{
			if($debug) \dash\notif::error(T_("Invalid nationality"), 'nationality');
			return false;
		}

		$nationalcode = \dash\app::request('nationalcode');
		if($nationalcode && !\dash\utility\filter::nationalcode($nationalcode))
		{
			if($debug) \dash\notif::error(T_("Invalid nationalcode syntax"), 'nationalcode');
			return false;
		}


		$pasportcode = \dash\app::request('pasportcode');
		$pasportcode = mb_strtolower($pasportcode);
		$pasportcode = \dash\utility\convert::to_en_number($pasportcode);
		if($pasportcode && mb_strlen($pasportcode) > 30 )
		{
			if($debug) \dash\notif::error(T_("Invalid pasportcode"), 'pasportcode');
			return false;
		}


		$birthday = null;
		if(\dash\app::isset_request('birthday'))
		{
			$birthday = \dash\app::request('birthday');
			if($birthday)
			{
				$birthday = \dash\date::db($birthday);
				$birthday = \dash\date::birthdate($birthday, true);

				if(!$birthday)
				{
					return false;
				}
			}
		}

		$pasportdate = \dash\app::request('pasportdate');
		$pasportdate = \dash\date::db($pasportdate);
		if($pasportdate === false)
		{
			if($debug) \dash\notif::error(T_("Invalid pasportdate"), 'pasportdate');
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
		if($gender && !in_array($gender, ['male','female', 'company', 'rather not say']))
		{
			if($debug) \dash\notif::error(T_("Invalid gender"), 'gender');
			return false;
		}

		$marital = \dash\app::request('marital');
		if($marital && !in_array($marital, ['single', 'married']))
		{
			if($debug) \dash\notif::error(T_("Invalid marital"), 'marital');
			return false;
		}

		$website = \dash\app::request('website');
		if($website && mb_strlen($website) > 100)
		{
			if($debug) \dash\notif::error(T_("Please set website less than 100 character"), 'website');
			return false;
		}

		$instagram = \dash\app::request('instagram');
		if($instagram && mb_strlen($instagram) > 100)
		{
			if($debug) \dash\notif::error(T_("Please set instagram less than 100 character"), 'instagram');
			return false;
		}

		$linkedin = \dash\app::request('linkedin');
		if($linkedin && mb_strlen($linkedin) > 100)
		{
			if($debug) \dash\notif::error(T_("Please set linkedin less than 100 character"), 'linkedin');
			return false;
		}

		$facebook = \dash\app::request('facebook');
		if($facebook && mb_strlen($facebook) > 100)
		{
			if($debug) \dash\notif::error(T_("Please set facebook less than 100 character"), 'facebook');
			return false;
		}

		$twitter = \dash\app::request('twitter');
		if($twitter && mb_strlen($twitter) > 100)
		{
			if($debug) \dash\notif::error(T_("Please set twitter less than 100 character"), 'twitter');
			return false;
		}


		$detail = [];

		if($_id)
		{
			if(isset($_load_user['detail']))
			{
				$detail = json_decode($_load_user['detail'], true);
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
			if($debug) \dash\notif::error(T_("Invalid shcode"), 'shcode');
			return false;
		}

		if($shcode && intval($shcode) > 1E+10)
		{
			if($debug) \dash\notif::error(T_("Invalid shcode"), 'shcode');
			return false;
		}

		if(\dash\app::isset_request('shcode'))
		{
			$detail['shcode'] = $shcode;
		}

		$birthcity = \dash\app::request('birthcity');
		if($birthcity && mb_strlen($birthcity) > 50)
		{
			if($debug) \dash\notif::error(T_("Invalid birthcity"), 'birthcity');
			return false;
		}

		if(\dash\app::isset_request('birthcity'))
		{
			$detail['birthcity'] = $birthcity;
		}


		$religion = \dash\app::request('religion');
		if($religion && mb_strlen($religion) > 50)
		{
			if($debug) \dash\notif::error(T_("Invalid religion"), 'religion');
			return false;
		}

		if(\dash\app::isset_request('religion'))
		{
			$detail['religion'] = $religion;
		}


		$avatar = \dash\app::request('avatar');
		if($avatar && mb_strlen($avatar) > 2000)
		{
			if($debug) \dash\notif::error(T_("Invalid avatar"), 'avatar');
			return false;
		}

		$education = \dash\app::request('education');
		if($education && mb_strlen($education) > 100)
		{
			if($debug) \dash\notif::error(T_("Invalid education"), 'education');
			return false;
		}

		if(\dash\app::isset_request('education'))
		{
			$detail['education'] = $education;
		}


		$educationcourse = \dash\app::request('educationcourse');
		if($educationcourse && mb_strlen($educationcourse) > 100)
		{
			if($debug) \dash\notif::error(T_("Invalid educationcourse"), 'educationcourse');
			return false;
		}

		if(\dash\app::isset_request('educationcourse'))
		{
			$detail['educationcourse'] = $educationcourse;
		}

		$shfrom = \dash\app::request('shfrom');
		if ($shfrom && mb_strlen($shfrom) > 200)
		{
			if($debug) \dash\notif::error(T_("Invalid issue place"), 'shfrom');
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
			if($debug) \dash\notif::error(T_("Invalid email"), 'email');
			return false;
		}

		if(\dash\app::isset_request('email'))
		{
			$detail['email'] = $email;
		}

		$phone = \dash\app::request('phone');
		if($phone && mb_strlen($phone) > 50)
		{
			if($debug) \dash\notif::error(T_("Invalid phone"), 'phone');
			return false;
		}

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['active','awaiting','deactive','removed','filter','unreachable']))
		{
			if($debug) \dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}


		if(\dash\app::isset_request('permission'))
		{
			$permission = \dash\app::request('permission');
			if(\dash\permission::check("cpUsersPermission"))
			{
				if($permission && !in_array($permission, array_keys(\dash\permission::groups())))
				{
					if($permission === 'supervisor')
					{
						if(!\dash\permission::supervisor())
						{
							if($debug) \dash\notif::error("Permission is incorrect", 'permission');
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
						if($debug) \dash\notif::error(T_("Permission is incorrect"), 'permission');
						return false;
					}
				}
			}

			$args['permission']          = $permission;

			if(isset($_load_user['permission']) && $_load_user['permission'] && $_load_user['permission'] !== $permission)
			{
				// when user change permission logout everyvere
				\dash\db\sessions::terminate_all($_id);
			}
		}


		if(!empty($detail))
		{
			$args['detail'] = json_encode($detail, JSON_UNESCAPED_UNICODE);
		}

		$twostep       = \dash\app::request('twostep') ? 1 : null;
		$forceremember = \dash\app::request('forceremember') ? 1 : 0;

		if(\dash\app::isset_request('sidebar'))
		{
			$sidebar         = \dash\app::request('sidebar') ? 1 : 0;
			$args['sidebar'] = $sidebar;
		}

		$password = \dash\app::request('password');

		if(\dash\permission::check("cpUsersPasswordChange"))
		{
			if($password)
			{
				if(mb_strlen($password) < 6)
				{
					if($debug) \dash\notif::error(T_("Plase set password larger than 6 character"), ['element' => ['password', 'repassword']]);
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
			if($debug) \dash\notif::error(T_("Plase set title less than 100 character"), 'title');
			return false;
		}

		$bio = \dash\app::request('bio');
		if($bio && mb_strlen($bio) > 100)
		{
			if($debug) \dash\notif::error(T_("Plase set bio less than 100 character"), 'bio');
			return false;
		}

		$displayname = \dash\app::request('displayname');
		if($displayname && mb_strlen($displayname) > 100)
		{
			if($debug) \dash\notif::error(T_("Plase set displayname less than 100 character"), 'displayname');
			return false;
		}


		$language = \dash\app::request('language');
		if($language && !\dash\language::check($language))
		{
			if($debug) \dash\notif::error(T_("Language is incorrect"), 'language');
			return false;
		}

		$username = \dash\app::request('username');
		if($username)
		{
			$username = \dash\utility\convert::to_en_number($username);

			$username = preg_replace("/\_{2,}/", "_", $username);
			$username = preg_replace("/\-{2,}/", "-", $username);

			if(mb_strlen($username) < 5)
			{
				\dash\notif::error(T_("Slug must have at least 5 character"), 'username');
				return false;
			}

			if(mb_strlen($username) > 50)
			{
				\dash\notif::error(T_("Please set the username less than 50 character"), 'username');
				return false;
			}

			if(!preg_match("/^[A-Za-z0-9_\-]+$/", $username))
			{
				\dash\notif::error(T_("Only [A-Za-z0-9_-] can use in username"), 'username');
				return false;
			}

			if(!preg_match("/[A-Za-z]+/", $username))
			{
				\dash\notif::error(T_("You must use a one character from [A-Za-z] in the username"), 'username');
				return false;
			}

			if(is_numeric($username))
			{
				\dash\notif::error(T_("Slug should contain a Latin letter"),'username');
				return false;
			}

			if(is_numeric(substr($username, 0, 1)))
			{
				\dash\notif::error(T_("The username must begin with latin letters"),'username');
				return false;
			}

			if(!preg_match("/^[A-Za-z0-9]+$/", $username))
			{
				\dash\notif::error(T_("Only [A-Za-z0-9] can use in username"), 'username', 'arguments');
				return false;
			}

			// check username
			if(mb_strlen($username) >= 50)
			{
				\dash\notif::error(T_("Username must be less than 50 character"), 'username', 'arguments');
				return false;
			}

			$username = mb_strtolower($username);

			if(in_array($username, []))
			{
				\dash\notif::error(T_("You can not choose this username"), 'username', 'arguments');
				return false;
			}

			$check_duplicate_username = \dash\db\users::get(['username' => $username, 'limit' => 1]);
			if(isset($check_duplicate_username['id']))
			{
				if(intval($check_duplicate_username['id']) === intval($_id))
				{
					// noproblem
				}
				else
				{
					if($debug) \dash\notif::error(T_("Duplicate username"), 'username');
					return false;
				}
				$args['username'] = $username;
			}
		}

		if(\dash\app::isset_request('username') && !$username)
		{
			$args['username'] = null;
		}



		// $tgstatus = \dash\app::request('tgstatus');
		// if($tgstatus && !in_array($tgstatus, ['active','deactive','spam','bot','block','unreachable','unknown','filter', 'awaiting', 'inline', 'callback']))
		// {
		// 	if($_option['debug']) if($debug) \dash\notif::error(T_("Invalid parameter tgstatus"), 'tgstatus');
		// 	return false;
		// }


		$type = \dash\app::request('type');
		if($type && mb_strlen($type) > 50)
		{
			if($_option['debug']) if($debug) \dash\notif::error(T_("You must set the type less than 50 character"), 'type');
			return false;
		}


		$parent = \dash\app::request('parent');
		$parent = \dash\coding::decode($parent);
		if(!$parent && \dash\app::request('parent'))
		{
			if($_option['debug']) if($debug) \dash\notif::error(T_("Parent is incorrect"), 'parent');
			return false;
		}



		// $tgusername = \dash\app::request('tgusername');
		// if($tgusername && mb_strlen($tgusername) > 100)
		// {
		// 	$tgusername = null;
		// }

		$pin = \dash\app::request('pin');
		if(($pin && mb_strlen($pin) > 4) || ($pin && !is_numeric($pin)))
		{
			if($_option['debug']) if($debug) \dash\notif::error(T_("Pin is incorrect"), 'pin');
			return false;
		}

		$ref = \dash\app::request('ref');
		$ref = \dash\coding::decode($ref);
		if(!$ref && \dash\app::request('ref'))
		{
			if($_option['debug']) if($debug) \dash\notif::error(T_("Ref is incorrect"), 'ref');
			return false;
		}


		$unit_id = \dash\app::request('unit_id');
		if($unit_id && !is_numeric($unit_id))
		{
			if($_option['debug']) if($debug) \dash\notif::error(T_("Unit id is incorrect"), 'unit_id');
			return false;
		}


		$chatid = \dash\app::request('chatid');

		$signature = \dash\app::request('signature');


		if(!\dash\permission::check("cpUsersPermission"))
		{
			unset($args['permission']);
		}

		if($_id && isset($_load_user['permission']))
		{
			if($_load_user['permission'] === 'supervisor')
			{
				unset($args['permission']);
			}
		}

		if(isset($args['permission']) && $args['permission'] === 'supervisor')
		{
			unset($args['permission']);
		}

		if(!$displayname && ($firstname || $lastname))
		{
			$displayname = trim($firstname. ' '. $lastname);
		}


		$theme = \dash\app::request('theme');
		if($theme && !\dash\utility\theme::check($theme))
		{
			\dash\notif::error(T_("Invalid theme id!"));
			return false;
		}


		$args['username']      = $username;
		$args['theme']         = $theme;
		$args['mobile']        = $mobile;
		$args['email']         = $email;

		$args['language']      = $language;
		$args['title']         = $title;
		$args['bio']           = $bio;
		$args['displayname']   = $displayname;
		$args['nationalcode']  = $nationalcode;
		$args['pasportcode']   = $pasportcode;
		$args['firstname']     = $firstname;
		$args['lastname']      = $lastname;
		$args['father']        = $father;
		$args['birthday']      = $birthday;
		$args['pasportdate']   = $pasportdate;
		$args['gender']        = $gender;
		$args['marital']       = $marital;
		$args['avatar']        = $avatar;
		$args['nationality']   = $nationality;
		$args['phone']         = $phone;
		$args['status']        = $status;
		$args['website']       = $website;
		$args['instagram']     = $instagram;
		$args['linkedin']      = $linkedin;
		$args['facebook']      = $facebook;
		$args['twitter']       = $twitter;
		$args['twostep']       = $twostep;
		$args['forceremember'] = $forceremember;
		$args['signature']     = $signature;
		$args['type']          = $type;
		$args['parent']        = $parent;
		$args['pin']           = $pin;
		$args['ref']           = $ref;
		$args['unit_id']       = $unit_id;
		// $args['tgstatus']      = $tgstatus;



		return $args;
	}


	/**
	 * ready data of user to _load_user in api
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
					$result['avatar_raw'] = $value;
					if($value)
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

				case 'forceremember':
					if($value === null)
					{
						if(\dash\option::config('enter', 'remember_me'))
						{
							$result[$key] = true;
						}
						else
						{
							$result[$key] = false;
						}
					}
					elseif($value)
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


	public static function user_in_all_table($_user_id)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		$result                 = [];
		$result['address']      =
		[
			'count'  => \dash\db\address::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['comments']     =
		[
			'count'  => \dash\db\comments::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['invoices']     =
		[
			'count'  => \dash\db\invoices::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['options']      =
		[
			'count'  => \dash\db\options::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['posts']        =
		[
			'count'  => \dash\db\posts::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['userdetail']   =
		[
			'count'  => \dash\db\userdetail::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['logs']         =
		[
			'count'  => \dash\db\logs::get_count(['from' => $_user_id]),
			'link'   => \dash\url::kingdom(). '/crm/log?from=',
			'encode' => false,
		];

		$result['logs_to']      =
		[
			'count'  => \dash\db\logs::get_count(['to' => $_user_id]),
			'link'   => \dash\url::kingdom(). '/crm/log?to=',
			'encode' => false,
		];

		$result['sessions']     =
		[
			'count'  => \dash\db\sessions::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];

		$result['transactions'] =
		[
			'count'  => \dash\db\transactions::get_count(['user_id' => $_user_id]),
			'link'   => null,
			'encode' => false,
		];



		if(is_callable(['\\lib\\user', 'user_in_all_table']))
		{
			$advance = \lib\user::user_in_all_table($_user_id);
			if(is_array($advance))
			{
				$result = array_merge($result, $advance);
			}
		}

		return $result;

	}


	public static function delete_user($_user_id)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		$deletedetail               = [];
		$deletedetail['remover']    = \dash\user::id();
		$deletedetail['deletedate'] = date("Y-m-d H:i:s");
		$loadAll                    = \dash\db\users::get(['id' => $_user_id, 'limit' => 1]);
		if(!is_array($loadAll))
		{
			$loadAll = [];
		}

		if(isset($loadAll['permission']) && $loadAll['permission'])
		{
			\dash\notif::error(T_("Can not remove user by permission"));
			return false;
		}

		if(isset($loadAll['status']) && $loadAll['status'] === 'removed')
		{
			\dash\notif::error(T_("This user was removed"));
			return false;
		}

		$deletedetail = array_merge($deletedetail, $loadAll);
		$deletedetail = json_encode($deletedetail, JSON_UNESCAPED_UNICODE);

		$update_old                         = [];

		$update_old['mobile']               = null;
		$update_old['email']                = null;
		$update_old['username']             = null;
		$update_old['displayname']          = null;
		$update_old['gender']               = null;
		$update_old['title']                = null;
		$update_old['password']             = null;
		$update_old['verifymobile']         = null;
		$update_old['verifyemail']          = null;
		$update_old['avatar']               = null;
		$update_old['parent']               = null;
		$update_old['type']                 = null;
		$update_old['pin']                  = null;
		$update_old['ref']                  = null;
		$update_old['twostep']              = null;
		$update_old['birthday']             = null;
		$update_old['unit_id']              = null;
		$update_old['language']             = null;
		$update_old['website']              = null;
		$update_old['facebook']             = null;
		$update_old['twitter']              = null;
		$update_old['instagram']            = null;
		$update_old['linkedin']             = null;
		$update_old['gmail']                = null;
		$update_old['sidebar']              = null;
		$update_old['firstname']            = null;
		$update_old['lastname']             = null;
		$update_old['bio']                  = null;
		$update_old['forceremember']        = null;
		$update_old['signature']            = null;
		$update_old['father']               = null;
		$update_old['nationality']          = null;
		$update_old['pasportdate']          = null;
		$update_old['marital']              = null;
		$update_old['foreign']              = null;
		$update_old['phone']                = null;
		$update_old['detail']               = null;
		$update_old['permission']           = null;

		$update_old['status']               = 'removed';

		$update_old['nationalcode']         = null;
		$update_old['pasportcode']          = null;
		$update_old['meta']                 = $deletedetail;

		\dash\db\users::update($update_old, $_user_id);

		\dash\log::set('crmMemberRemoved', ['code' => $_user_id]);
		return true;

	}

}
?>