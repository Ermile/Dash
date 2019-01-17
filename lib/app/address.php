<?php
namespace dash\app;

/**
 * Class for address.
 */
class address
{
	public static $sort_field =
	[
		'subdomain',
		'title',
		'firstname',
		'lastname',
		'company',
		'companyname',
		'jobtitle',
		'country',
		'province',
		'city',
		'phone',
		'fax',
		'status',
		'favorite',
		'isdefault',
	];


	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}

		$result = \dash\db\address::get(['id' => $id, 'limit' => 1]);
		$temp = [];
		if(is_array($result))
		{
			$temp = self::ready($result);
		}
		return $temp;
	}


	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function check($_id = null)
	{

		$title = \dash\app::request('title');
		if($title && mb_strlen($title) >= 100)
		{
			\dash\notif::error(T_("Please set title less than 100 character"), 'title');
			return false;
		}

		$name = \dash\app::request('name');
		if($name && mb_strlen($name) > 100)
		{
			\dash\notif::error(T_("Please set name less than 100 character"), 'name');
			return false;
		}


		$isdefault = \dash\app::request('isdefault') ? 1 : null;

		$company = \dash\app::request('company') ? 1 : null;

		$companyname = \dash\app::request('companyname');
		if($companyname && mb_strlen($companyname) > 100)
		{
			\dash\notif::error(T_("Please set companyname less than 100 character"), 'companyname');
			return false;
		}

		$jobtitle = \dash\app::request('jobtitle');
		if($jobtitle && mb_strlen($jobtitle) > 100)
		{
			\dash\notif::error(T_("Please set jobtitle less than 100 character"), 'jobtitle');
			return false;
		}

		$country = \dash\app::request('country');

		if($country && mb_strlen($country) > 100)
		{
			\dash\notif::error(T_("Please set country less than 100 character"), 'country');
			return false;
		}

		if($country && !\dash\utility\location\countres::check($country))
		{
			\dash\notif::error(T_("Invalid country"), 'country');
			return false;
		}

		$province = \dash\app::request('province');
		if($province && mb_strlen($province) > 100)
		{
			\dash\notif::error(T_("Please set province less than 100 character"), 'province');
			return false;
		}

		if($province && !\dash\utility\location\provinces::check($province))
		{
			\dash\notif::error(T_("Invalid province"), 'province');
			return false;
		}

		$city = \dash\app::request('city');
		if($city && mb_strlen($city) > 100)
		{
			\dash\notif::error(T_("Please set city less than 100 character"), 'city');
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

		$address = \dash\app::request('address');
		if($address && mb_strlen($address) > 300)
		{
			\dash\notif::error(T_("Please set address less than 300 character"), 'address');
			return false;
		}

		if(!$address)
		{
			\dash\notif::error(T_("Please fill the address"), 'address');
			return false;
		}

		$address2 = \dash\app::request('address2');
		if($address2 && mb_strlen($address2) > 300)
		{
			\dash\notif::error(T_("Please set address2 less than 300 character"), 'address2');
			return false;
		}

		$subdomain = \dash\app::request('subdomain');
		if($subdomain && mb_strlen($subdomain) > 50)
		{
			\dash\notif::error(T_("Please set subdomain less than 50 character"), 'subdomain');
			return false;
		}

		$mobile = \dash\app::request('mobile');
		if($mobile && !\dash\utility\filter::mobile($mobile))
		{
			\dash\notif::error(T_("Invalid mobile"), 'mobile');
			return false;
		}

		$postcode = \dash\app::request('postcode');
		if($postcode && mb_strlen($postcode) > 50)
		{
			\dash\notif::error(T_("Please set postcode less than 50 character"), 'postcode');
			return false;
		}

		$phone = \dash\app::request('phone');
		if($phone && mb_strlen($phone) > 50)
		{
			\dash\notif::error(T_("Please set phone less than 50 character"), 'phone');
			return false;
		}

		$fax = \dash\app::request('fax');
		if($fax && mb_strlen($fax) > 50)
		{
			\dash\notif::error(T_("Please set fax less than 50 character"), 'fax');
			return false;
		}

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['enable','disable','filter','leave','spam','delete']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}

		$favorite = \dash\app::request('favorite') ? 1 : null;

		$user_id = \dash\app::request('user_id');
		if($user_id && !is_numeric($user_id))
		{
			\dash\notif::error(T_("Invalid user id"));
			return false;
		}

		$args                = [];
		$args['user_id']       = $user_id;
		$args['title']       = $title;
		$args['name']        = $name;
		$args['mobile']      = $mobile;
		$args['isdefault']   = $isdefault;
		$args['company']     = $company;
		$args['companyname'] = $companyname;
		$args['subdomain']   = $subdomain;
		$args['jobtitle']    = $jobtitle;
		$args['country']     = $country;
		$args['province']    = $province;
		$args['city']        = $city;
		$args['address']     = $address;
		$args['address2']    = $address2;
		$args['postcode']    = $postcode;
		$args['phone']       = $phone;
		$args['fax']         = $fax;
		$args['status']      = $status;
		$args['favorite']    = $favorite;

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
		$result['location_string'] = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'user_id':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				case 'country':
					$result[$key] = $value;
					$result['country_name'] = \dash\utility\location\countres::get_localname($value, true);
					$result['location_string'][1] = $result['country_name'];
					break;


				case 'province':
					$result[$key] = $value;
					$result['province_name'] = \dash\utility\location\provinces::get_localname($value);
					$result['location_string'][2] = $result['province_name'];
					break;

				case 'city':
					$result[$key] = $value;
					$result['city_name'] = \dash\utility\location\cites::get_localname($value);
					$result['location_string'][3] = $result['city_name'];
					break;

				case 'map':
					if($value && is_string($value))
					{
						$result[$key] = json_decode($value, true);
					}
					else
					{
						$result[$key] = $value;
					}
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}
		ksort($result['location_string']);
		$result['location_string'] = array_filter($result['location_string']);
		$result['location_string'] = implode(T_(","). " ", $result['location_string']);

		return $result;
	}


	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{
		\dash\app::variable($_args);


		$default_option =
		[
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\dash\user::id())
		{
			\dash\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return  = [];

		if(!$args['status'])
		{
			$args['status'] = 'enable';
		}

		if(!$args['user_id'])
		{
			$args['user_id'] = \dash\user::id();
		}

		// if(\dash\url::subdomain())
		// {
		// 	$args['subdomain'] = \dash\url::subdomain();
		// }

		$address = \dash\db\address::insert($args);

		if(!$address)
		{
			\dash\log::set('noWayToAddAddress');
			\dash\notif::error(T_("No way to insert address"));
			return false;
		}

		\dash\log::set('addAddress');

		return $return;
	}


	public static function list($_string = null, $_args = [])
	{

		if(!\dash\user::id())
		{
			return false;
		}

		$default_args =
		[
			'order' => null,
			'sort'  => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$option = [];
		$option = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				unset($option['order']);
			}
		}

		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		$result = \dash\db\address::search($_string, $option, $field);

		$temp            = [];


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


	public static function edit($_args, $_id)
	{
		\dash\app::variable($_args);

		$id = \dash\coding::decode($_id);

		if(!$id)
		{
			\dash\notif::error(T_("Can not access to edit address"), 'address');
			return false;
		}

		if(!\dash\user::id())
		{
			return false;
		}

		// check args
		$args = self::check($id);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		if(!\dash\app::isset_request('title')) unset($args['title']);
		if(!\dash\app::isset_request('name')) unset($args['name']);
		if(!\dash\app::isset_request('user_id')) unset($args['user_id']);
		if(!\dash\app::isset_request('isdefault')) unset($args['isdefault']);
		if(!\dash\app::isset_request('company')) unset($args['company']);
		if(!\dash\app::isset_request('companyname')) unset($args['companyname']);
		if(!\dash\app::isset_request('jobtitle')) unset($args['jobtitle']);
		if(!\dash\app::isset_request('country')) unset($args['country']);
		if(!\dash\app::isset_request('province')) unset($args['province']);
		if(!\dash\app::isset_request('city')) unset($args['city']);
		if(!\dash\app::isset_request('address')) unset($args['address']);
		if(!\dash\app::isset_request('address2')) unset($args['address2']);
		if(!\dash\app::isset_request('postcode')) unset($args['postcode']);
		if(!\dash\app::isset_request('phone')) unset($args['phone']);
		if(!\dash\app::isset_request('fax')) unset($args['fax']);
		if(!\dash\app::isset_request('status')) unset($args['status']);
		if(!\dash\app::isset_request('favorite')) unset($args['favorite']);
		if(!\dash\app::isset_request('subdomain')) unset($args['subdomain']);
		if(!\dash\app::isset_request('mobile')) unset($args['mobile']);

		if(!empty($args))
		{
			\dash\db\address::update($args, $id);
			\dash\log::set('editAddress');
		}

		return true;
	}


	public static function remove($_id)
	{
		$id = \dash\coding::decode($_id);

		if(!$id)
		{
			\dash\notif::error(T_("Can not access to remove this address"));
			return false;
		}

		if(!\dash\user::id())
		{
			return false;
		}

		$check = ['user_id' => \dash\user::id(), 'id' => $id, 'limit' => 1];
		$check = \dash\db\address::get($check);
		if(!isset($check['id']))
		{
			\dash\notif::error(T_("Can not access to remove this address"));
			return false;
		}

		\dash\db\address::update(['status' => 'delete'], $id);
		\dash\notif::ok(T_("Address removed"));
		return true;
	}


	public static function remove_admin($_id)
	{
		$id = \dash\coding::decode($_id);

		if(!$id)
		{
			\dash\notif::error(T_("Can not access to remove this address"));
			return false;
		}

		if(!\dash\user::id())
		{
			return false;
		}

		$check = ['id' => $id, 'limit' => 1];
		$check = \dash\db\address::get($check);
		if(!isset($check['id']))
		{
			\dash\notif::error(T_("Can not access to remove this address"));
			return false;
		}

		\dash\db\address::update(['status' => 'delete'], $id);
		\dash\notif::ok(T_("Address removed"));
		return true;
	}
}
?>