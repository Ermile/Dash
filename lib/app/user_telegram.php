<?php
namespace dash\app;


class user_telegram
{


	public static $sort_field =
	[
		'id',
		'user_id',
		'chatid',
		'firstname',
		'lastname',
		'username',
		'language',
		'status',
		'lastupdate',
		'datecreated',
	];


	public static function dataTableList(&$dataTable)
	{
		$id = array_column($dataTable, 'id');
		$id = array_map(['\\dash\\coding', 'decode'], $id);
		if(!$id)
		{
			return;
		}
		$load = \dash\db\user_telegram::get_dataTable(implode(',', $id));
		if(!is_array($load))
		{
			return;
		}

		$load = array_combine(array_column($load, 'user_id'), $load);

		foreach ($dataTable as $key => $value)
		{
			$myId = \dash\coding::decode($value['id']);
			if(isset($load[$myId]))
			{
				$dataTable[$key]['chatid'] = $load[$myId]['chatid'];
			}
		}
	}


	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}

		$result = \dash\db\user_telegram::get(['id' => $id, 'limit' => 1]);
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

		$user_id = \dash\app::request('user_id');
		if(!$user_id || !is_numeric($user_id))
		{
			\dash\notif::error(T_("Invalid user id"), 'user_id');
			return false;
		}

		$chatid = \dash\app::request('chatid');
		if(!$chatid || !is_numeric($chatid))
		{
			\dash\notif::error(T_("Invalid user id"), 'chatid');
			return false;
		}

		$firstname = \dash\app::request('firstname');
		if($firstname && mb_strlen($firstname) >= 200)
		{
			\dash\notif::error(T_("Please set firstname less than 200 character"), 'firstname');
			return false;
		}

		$lastname = \dash\app::request('lastname');
		if($lastname && mb_strlen($lastname) >= 200)
		{
			\dash\notif::error(T_("Please set lastname less than 200 character"), 'lastname');
			return false;
		}

		$username = \dash\app::request('username');
		if($username && mb_strlen($username) >= 200)
		{
			\dash\notif::error(T_("Please set username less than 200 character"), 'username');
			return false;
		}

		$language = \dash\app::request('language');
		if($language && mb_strlen($language) !== 2)
		{
			\dash\notif::error(T_("Invalid language"), 'language');
			return false;
		}


		$status = \dash\app::request('status');
		if($status && !in_array($status, ['active','deactive','spam','bot','block','unreachable','unknown','filter','awaiting','inline','callback']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}

		$args              = [];
		$args['firstname'] = $firstname;
		$args['lastname']  = $lastname;
		$args['username']  = $username;
		$args['language']  = $language;
		$args['user_id']   = $user_id;
		$args['chatid']    = $chatid;
		$args['status']    = $status;
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

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}


	public static function remove($_chat_id, $_user_id)
	{
		if(!$_chat_id || !$_user_id || !is_numeric($_chat_id) || !is_numeric($_user_id))
		{
			return false;
		}

		$check_duplicate =
		[
			'user_id' => $_user_id,
			'chatid'  => $_chat_id,
			'limit'   => 1,
		];

		$check_duplicate = \dash\db\user_telegram::get($check_duplicate);
		if(isset($check_duplicate['id']))
		{
			\dash\log::set('removeUserTelegram', ['oldrecord' => $check_duplicate]);
			\dash\db\user_telegram::hard_delete($check_duplicate['id']);
			return true;
		}
		else
		{
			\dash\notif::error(T_("This user have not this chatid"));
			return false;
		}


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

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return  = [];

		$check_duplicate =
		[
			// 'user_id' => $args['user_id'],
			'chatid'  => $args['chatid'],
			'limit'   => 1,
		];

		$check_duplicate = \dash\db\user_telegram::get($check_duplicate);
		if(isset($check_duplicate['id']))
		{
			\dash\log::set('tryToInsertDuplicateUserTelegram');
			\dash\notif::error(T_("Duplicate chatid"));
			return false;
		}

		$user_telegram = \dash\db\user_telegram::insert($args);

		if(!$user_telegram)
		{
			\dash\log::set('noWayToAddUserTelegram');
			\dash\notif::error(T_("No way to insert user_telegram"));
			return false;
		}

		\dash\log::set('addUserTelegram');

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

		$result = \dash\db\user_telegram::search($_string, $option, $field);

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
			\dash\notif::error(T_("Can not access to edit user_telegram"), 'user_telegram');
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

		if(!\dash\app::isset_request('firstname')) unset($args['firstname']);
		if(!\dash\app::isset_request('lastname')) unset($args['lastname']);
		if(!\dash\app::isset_request('username')) unset($args['username']);
		if(!\dash\app::isset_request('language')) unset($args['language']);
		if(!\dash\app::isset_request('user_id')) unset($args['user_id']);
		if(!\dash\app::isset_request('chatid')) unset($args['chatid']);
		if(!\dash\app::isset_request('status')) unset($args['status']);

		if(!empty($args))
		{
			\dash\db\user_telegram::update($args, $id);
			\dash\log::set('editUserTelegram');
		}

		return true;
	}

}
?>