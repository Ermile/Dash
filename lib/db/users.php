<?php
namespace dash\db;


class users
{

	public static $USERS_DETAIL = [];

	public static $user_id;


	public static function all_user_mobile($_where = null)
	{
		$where = null;

		if($_where)
		{
			$where = \dash\db\config::make_where($_where);
			if($where)
			{
				$where = " AND $where";
			}
		}

		$query = "SELECT users.mobile AS `mobile` FROM users WHERE users.mobile IS NOT NULL  $where";

		return \dash\db::get($query, 'mobile');
	}



	public static function update_where($_set, $_where)
	{
		return \dash\db\config::public_update_where('users', ...func_get_args());
	}


	public static function get()
	{
		$result = \dash\db\config::public_get('users', ...func_get_args());
		return $result;
	}


	public static function hard_delete($_id)
	{
		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "DELETE FROM users WHERE users.id = $_id LIMIT 1";
		return \dash\db::query($query);
	}


	public static function get_ref_count($_args)
	{
		$where = \dash\db\config::make_where($_args);
		if($where)
		{
			$query = "SELECT COUNT(*) AS `count` FROM users WHERE $where ";
			return \dash\db::get($query, 'count', true);
		}
	}


	public static function get_by_mobile($_mobile)
	{
		$args =
		[
			'mobile' => $_mobile,
			'limit'  => 1
		];
		$result = self::get($args);
		return $result;
	}


	public static function get_by_id($_user_id)
	{
		$args =
		[
			'id'    => $_user_id,
			'limit' => 1
		];
		return self::get($args);
	}


	public static function get_by_email($_email, $_field = false)
	{
		$query = "SELECT * FROM users WHERE users.email = '$_email' AND users.status != 'removed' ORDER BY users.id DESC LIMIT 1 ";
		return \dash\db::get($query, null, true);
	}


	public static function get_by_username($_username)
	{
		$args =
		[
			'username' => $_username,
			'limit'         => 1
		];
		return self::get($args);
	}

	public static function search($_string = null, $_options = [])
	{
		if(!is_array($_options))
		{
			$_options = [];
		}
		$default_options['search_field'] =
		"
			(
				users.mobile LIKE '%__string__%' OR
				users.displayname LIKE '%__string__%'
			)
		";

		$_options = array_merge($default_options, $_options);

		// public_show_field
		return \dash\db\config::public_search('users', $_string, $_options);
	}



	private static function insert()
	{
		return \dash\db\config::public_insert('users', ...func_get_args());
	}


	public static function update()
	{
		return \dash\db\config::public_update('users', ...func_get_args());
	}


	private static function check_ref($_ref)
	{
		if(!is_string($_ref))
		{
			return null;
		}

		if($_ref)
		{
			$ref_id = \dash\coding::decode($_ref);
			if($ref_id)
			{
				$check_ref = self::get($ref_id);
				if(!empty($check_ref))
				{
					return $ref_id;
				}
			}
		}
		return null;
	}



	/**
	 * check signup and if can add new user
	 * @return [type] [description]
	 */
	public static function signup($_args = [])
	{
		$default_args =
		[
			'mobile'       => null,
			'password'     => null,
			'email'        => null,
			'permission'   => null,
			'displayname'  => null,
			'ref'          => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$ref = null;
		// get the ref and set in users_parent
		if(isset($_SESSION['ref']))
		{
			$ref = self::check_ref($_SESSION['ref']);
			if($ref)
			{
				$_args['ref'] = $_SESSION['ref'];
			}
			else
			{
				$_args['ref'] = null;
			}
		}
		elseif($_args['ref'])
		{
			$ref = self::check_ref($_args['ref']);
			if(!$ref)
			{
				$_args['ref'] = null;
			}
		}

		if($ref)
		{
			unset($_SESSION['ref']);
		}

		if(isset($_args['mobile']) && $_args['mobile'])
		{
			$mobile = \dash\utility\filter::mobile($_args['mobile']);
			if(!$mobile)
			{
				return false;
			}

			$check = self::get_by_mobile($mobile);

			if(isset($check['id']))
			{
				return false;
			}
		}


		if(isset($_args['chatid']) && $_args['chatid'])
		{
			$check_chatid = self::get(['chatid' => $chatid, 'limit' => 1]);

			if(isset($check_chatid['id']))
			{
				return false;
			}
		}

		if(isset($_args['email']) && $_args['email'])
		{
			$check_email = self::get(['email' => $email, 'limit' => 1]);

			if(isset($check_email['id']))
			{
				return false;
			}
		}


		if($_args['password'])
		{
			$password = \dash\utility::hasher($_args['password']);
		}
		else
		{
			$password = null;
		}

		if(!\dash\engine\process::status())
		{
			return false;
		}

		if(mb_strlen($_args['displayname']) > 99)
		{
			$_args['displayname'] = null;
		}

		// signup up users
		$_args['datecreated'] = date("Y-m-d H:i:s");

		$insert_new    = self::insert($_args);
		$insert_id     = \dash\db::insert_id();
		return $insert_id;

	}


	public static function get_count()
	{
		return \dash\db\config::public_get_count('users', ...func_get_args());
	}


	public static function permission_group()
	{
		$query = "SELECT COUNT(*) AS `count`, users.permission AS `permission` FROM users GROUP BY users.permission";
		return \dash\db::get($query, ['permission', 'count']);
	}



	public static function find_user_to_login($_find)
	{
		if(!$_find)
		{
			return false;
		}

		$query_mobile = null;
		if($temp_mobile = \dash\utility\filter::mobile($_find))
		{
			$query_mobile = " OR users.mobile = '$temp_mobile' ";
		}

		$query = "SELECT * FROM users WHERE users.email = '$_find' OR users.username = '$_find' $query_mobile LIMIT 1";

		$is_in_users = \dash\db::get($query, null, true);
		if($is_in_users)
		{
			return $is_in_users;
		}

		// must be load contact info to find user and if not found:
		return false;
	}
}
?>
