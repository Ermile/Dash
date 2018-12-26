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
				$msg = "<a href='". \dash\url::kingdom(). '/a/member?q='. $nationalcode_q. "'>$msg</a>";
				\dash\notif::error($msg, ['nationalcode', 'pasportcode']);
				return false;
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

		\dash\log::set('addNewMember', ['code' => $user_id]);

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
			if($_passport_code)
			{
				// check pasportcode only
				$result = \dash\db\users::get(['pasportcode' => "$_passport_code", 'limit' => 1]);
			}
			else
			{
				\dash\notif::error(T_("Nationalcode or pasportcode is required"), ['nationalcode', 'passportcode']);
				return true;
			}
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

		if(isset($_args['step_export']))
		{
			\dash\log::set('memberExportCsvFile');
			set_time_limit(60 * 10);
			ini_set('memory_limit', '-1');
			ini_set("max_execution_time", "-1");

			$_args['pagenation'] = false;
			$_args['limit']      = 1000;
			$my_limit            = 1000;
			$link                = null;
			$result            = \dash\db\users::search($_string, $_args);
			while ($result)
			{
				$result = array_map(['self', 'ready'], $result);
				$link = \lib\app\member\export::csv($result);
				$_args['start_limit'] = $my_limit;
				$_args['end_limit']   = 1000;
				$result               = \dash\db\users::search($_string, $_args);
				$my_limit             = $my_limit + 1000;
			}

			$msg = T_("Create export file completed");
			$msg .= '<a href="'. $link. '" download > <b>'. T_("To download it click here"). '</b> </a>';
			$msg .= '<br>'. T_("This file will be automatically deleted for a few minutes");
			\dash\notif::ok($msg, ['timeout' => 999999]);
			return true;
		}
		else
		{
			$result            = \dash\db\users::search($_string, $_args);
		}

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
					$msg = "<a href='". \dash\url::kingdom(). '/a/member?q='. $nationalcode_q. "'>$msg</a>";
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
		if(!\dash\app::isset_request('shfrom'))         unset($args['shfrom']);
		if(!\dash\app::isset_request('firstname'))      unset($args['firstname']);
		if(!\dash\app::isset_request('lastname'))       unset($args['lastname']);
		if(!\dash\app::isset_request('father'))         unset($args['father']);
		if(!\dash\app::isset_request('nationalcode'))   unset($args['nationalcode']);
		if(!\dash\app::isset_request('pasportcode'))    unset($args['pasportcode']);
		if(!\dash\app::isset_request('birthdate'))      unset($args['birthdate']);
		if(!\dash\app::isset_request('pasportdate'))    unset($args['pasportdate']);
		if(!\dash\app::isset_request('gender'))         unset($args['gender']);
		if(!\dash\app::isset_request('marital'))        unset($args['marital']);
		if(!\dash\app::isset_request('shcode'))         unset($args['shcode']);
		if(!\dash\app::isset_request('birthcity'))      unset($args['birthcity']);
		if(!\dash\app::isset_request('zipcode'))        unset($args['zipcode']);
		if(!\dash\app::isset_request('religion'))       unset($args['religion']);
		if(!\dash\app::isset_request('avatar'))         unset($args['avatar']);
		if(!\dash\app::isset_request('education'))      unset($args['education']);
		if(!\dash\app::isset_request('education2'))     unset($args['education2']);
		if(!\dash\app::isset_request('educationcourse')) unset($args['educationcourse']);
		if(!\dash\app::isset_request('city'))           unset($args['city']);
		if(!\dash\app::isset_request('province'))       unset($args['province']);
		if(!\dash\app::isset_request('country'))        unset($args['country']);
		if(!\dash\app::isset_request('address'))        unset($args['address']);
		if(!\dash\app::isset_request('phone'))          unset($args['phone']);
		if(!\dash\app::isset_request('mobile2'))        unset($args['mobile2']);
		if(!\dash\app::isset_request('fathermobile'))   unset($args['fathermobile']);
		if(!\dash\app::isset_request('mothermobile'))   unset($args['mothermobile']);
		if(!\dash\app::isset_request('status'))         unset($args['status']);
		if(!\dash\app::isset_request('desc'))           unset($args['desc']);
		if(!\dash\app::isset_request('foreign'))        unset($args['foreign']);
		if(!\dash\app::isset_request('nationality'))    unset($args['nationality']);


		if(!empty($args))
		{
			$update = \dash\db\users::update($args, $id);
			\dash\log::set('editMember', ['code' => $id]);

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

		if(\dash\permission::check('aMemberSkipRequiredField'))
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
				\dash\notif::error(T_("Firstname or lastname is required"), 'firstname');
				return false;
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


		// if(\dash\app::isset_request('nationalcode') || \dash\app::isset_request('pasportcode'))
		// {
		// 	if(!$nationalcode && !$pasportcode && !$force_add)
		// 	{
		// 		if($nationality === 'IR')
		// 		{
		// 			\dash\notif::error(T_("National code or pasportcode is required"), ['element' => ['nationalcode']]);
		// 		}
		// 		else
		// 		{
		// 			\dash\notif::error(T_("National code or pasportcode is required"), ['element' => ['pasportcode']]);
		// 		}
		// 		return false;
		// 	}
		// }

		if($nationality && $nationality !== 'IR' && \dash\app::isset_request('nationalcode') && $nationalcode)
		{
			\dash\notif::error(T_("Please remove the nationalcode"), ['element' => ['nationalcode']]);
			return false;
		}

		$birthdate = null;

		if(\dash\app::isset_request('birthdate'))
		{
			$birthdate = \dash\app::request('birthdate');
			$birthdate = \dash\date::db($birthdate);
			$birthdate = \dash\date::birthdate($birthdate, true);

			// if(!$birthdate && !$force_add)
			// {
			// 	\dash\notif::error(T_("Birthdate is required"), 'birthdate');
			// 	return false;
			// }
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
			if(!$gender && !$force_add)
			{
				\dash\notif::error(T_('Gender is required'), 'gender');
				return false;
			}
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

		$birthcity = \dash\app::request('birthcity');
		if($birthcity && mb_strlen($birthcity) > 50)
		{
			\dash\notif::error(T_("Invalid birthcity"), 'birthcity');
			return false;
		}

		$zipcode = \dash\app::request('zipcode');
		$zipcode = \dash\utility\convert::to_en_number($zipcode);
		if($zipcode && !is_numeric($zipcode))
		{
			\dash\notif::error(T_("Invalid zipcode"), 'zipcode');
			return false;
		}

		if($zipcode && !intval($zipcode) > 1E+10)
		{
			\dash\notif::error(T_("Invalid zipcode"), 'zipcode');
			return false;
		}

		$religion = \dash\app::request('religion');
		if($religion && mb_strlen($religion) > 50)
		{
			\dash\notif::error(T_("Invalid religion"), 'religion');
			return false;
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

		$education2 = \dash\app::request('education2');
		if($education2 && mb_strlen($education2) > 100)
		{
			\dash\notif::error(T_("Invalid education2"), 'education2');
			return false;
		}

		$educationcourse = \dash\app::request('educationcourse');
		if($educationcourse && mb_strlen($educationcourse) > 100)
		{
			\dash\notif::error(T_("Invalid educationcourse"), 'educationcourse');
			return false;
		}

		$shfrom = \dash\app::request('shfrom');
		if ($shfrom && mb_strlen($shfrom) > 200)
		{
			\dash\notif::error(T_("Invalid issue place"), 'shfrom');
			return false;
		}

		$email = \dash\app::request('email');
		if ($email && mb_strlen($email) > 150)
		{
			\dash\notif::error(T_("Invalid email"), 'email');
			return false;
		}

		$city = \dash\app::request('city');
		if($city && !\dash\utility\location\cites::check($city))
		{
			\dash\notif::error(T_("Invalid city"), 'city');
			return false;
		}

		$province = \dash\app::request('province');
		if($province && !\dash\utility\location\provinces::check($province))
		{
			\dash\notif::error(T_("Invalid province"), 'province');
			return false;
		}

		if(!$province && $city)
		{
			$province = \dash\utility\location\cites::get($city, 'province', 'province');
			if(!\dash\utility\location\provinces::check($province))
			{
				$province = null;
			}
		}

		$country = \dash\app::request('country');
		if($country && !\dash\utility\location\countres::check($country))
		{
			\dash\notif::error(T_("Invalid country"), 'country');
			return false;
		}

		$address = \dash\app::request('address');
		if($address && mb_strlen($address) > 500)
		{
			\dash\notif::error(T_("Invalid address"), 'address');
			return false;
		}

		$phone = \dash\app::request('phone');
		if($phone && mb_strlen($phone) > 50)
		{
			\dash\notif::error(T_("Invalid phone"), 'phone');
			return false;
		}

		$mobile2 = \dash\app::request('mobile2');
		if($mobile2 && !\dash\utility\filter::mobile($mobile2))
		{
			\dash\notif::error(T_("Invalid mobile2"), 'mobile2');
			return false;
		}

		$fathermobile = \dash\app::request('fathermobile');
		if($fathermobile && !\dash\utility\filter::mobile($fathermobile))
		{
			\dash\notif::error(T_("Invalid fathermobile"), 'fathermobile');
			return false;
		}

		$mothermobile = \dash\app::request('mothermobile');
		if($mothermobile && !\dash\utility\filter::mobile($mothermobile))
		{
			\dash\notif::error(T_("Invalid mothermobile"), 'mothermobile');
			return false;
		}

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['active','awaiting','deactive','removed','filter','unreachable']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}

		$desc = \dash\app::request('desc');

		$bank = \dash\app::request('bank');
		if($bank && mb_strlen($bank) > 200)
		{
			\dash\notif::error(T_("Bank name is too large"), 'bank');
			return false;
		}

		$accountnumber = \dash\app::request('accountnumber');
		if($accountnumber && mb_strlen($accountnumber) > 200)
		{
			\dash\notif::error(T_("Account number is too large"), 'accountnumber');
			return false;
		}

		if($accountnumber && !is_numeric($accountnumber))
		{
			\dash\notif::error(T_("Account number must be a number"), 'accountnumber');
			return false;
		}

		$shaba = \dash\app::request('shaba');
		if($shaba && mb_strlen($shaba) > 200)
		{
			\dash\notif::error(T_("Shaba number is too large"), 'shaba');
			return false;
		}

		if($shaba && !is_numeric($shaba))
		{
			\dash\notif::error(T_("Shaba number must be a number"), 'shaba');
			return false;
		}

		$cardnumber = \dash\app::request('cardnumber');
		if($cardnumber && mb_strlen($cardnumber) > 200)
		{
			\dash\notif::error(T_("Shaba number is too large"), 'cardnumber');
			return false;
		}

		if($cardnumber && !is_numeric($cardnumber))
		{
			\dash\notif::error(T_("Shaba number must be a number"), 'cardnumber');
			return false;
		}

		$tablerows = \dash\app::request('tablerows');
		if($tablerows && !is_numeric($tablerows))
		{
			\dash\notif::error(T_("Please set the table rows as a number"), 'tablerows');
			return false;
		}




		$permission = \dash\app::request('permission');
		if(\dash\permission::check("aMemberPermissionChange"))
		{
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
					\dash\app::log('addon:api:user:permission:max:lenght', \dash\user::id(), $log_meta);
					\dash\notif::error(T_("Permission is incorrect"), 'permission');
					return false;
				}
			}
		}





		$args['mobile']          = $mobile;

		$args['email']           = $email;
		$args['shfrom']          = $shfrom;
		$args['nationalcode']    = $nationalcode;
		$args['pasportcode']     = $pasportcode;
		$args['firstname']       = $firstname;
		$args['lastname']        = $lastname;
		$args['father']          = $father;
		$args['birthday']       = $birthdate;
		$args['pasportdate']     = $pasportdate;
		$args['gender']          = $gender;
		$args['marital']         = $marital;
		$args['shcode']          = $shcode;
		$args['birthcity']       = $birthcity;
		$args['zipcode']         = $zipcode;
		$args['religion']        = $religion;
		$args['avatar']          = $avatar;
		$args['education']       = $education;
		$args['education2']      = $education2;
		$args['educationcourse'] = $educationcourse;
		$args['city']            = $city;
		$args['province']        = $province;
		$args['nationality']     = $nationality;
		$args['country']         = $country;
		$args['address']         = $address;
		$args['phone']           = $phone;
		$args['mobile2']         = $mobile2;
		$args['fathermobile']    = $fathermobile;
		$args['mothermobile']    = $mothermobile;
		$args['status']          = $status;
		$args['desc']            = $desc;



		$args['permission']          = $permission;


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

				case 'user_id':
				case 'creator':
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