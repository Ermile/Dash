<?php
namespace dash\db;

/** users account managing **/
class users
{
	/**
	 * this library work with acoount
	 * v1.2
	 */
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


	/**
	 * get users data in users table
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function get()
	{
		return \dash\db\config::public_get('users', ...func_get_args());
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


	/**
	 * Gets in database.
	 * get user data in database
	 *
	 * @param      <type>  $_database_name  The database name
	 * @param      <type>  $_args           The arguments
	 */
	public static function get_in_database($_database_name, $_where)
	{
		if(!$_database_name || !is_string($_database_name))
		{
			return false;
		}

		$only_one_value = false;
		$limit          = null;

		if(isset($_where['limit']))
		{
			if($_where['limit'] === 1)
			{
				$only_one_value = true;
			}

			$limit = " LIMIT $_where[limit] ";
		}

		unset($_where['limit']);

		$where = \dash\db\config::make_where($_where);

		if(!$where)
		{
			return false;
		}

		$query = "SELECT * FROM `$_database_name`.`users` WHERE $where $limit ";

		$result = \dash\db::get($query, null, $only_one_value);

		return $result;
	}


	/**
	 * Gets the reference count.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function get_ref_count($_args)
	{
		$where = \dash\db\config::make_where($_args);
		if($where)
		{
			$query = "SELECT COUNT(*) AS `count` FROM users WHERE $where ";
			return \dash\db::get($query, 'count', true);
		}
	}


	/**
	 * Gets the by identifier.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The by identifier.
	 */
	public static function get_by_id($_user_id)
	{
		$args =
		[
			'id'    => $_user_id,
			'limit' => 1
		];
		return self::get($args);
	}


	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
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


	/**
	 * get all data by email
	 *
	 * @param      <type>  $_email  The email
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_by_email($_email, $_field = false)
	{
		$query = "SELECT * FROM users WHERE users.email = '$_email' AND users.status != 'removed' ORDER BY users.id DESC LIMIT 1 ";
		return \dash\db::get($query, null, true);
	}


	/**
	 * get all data by username
	 *
	 * @param      <type>  $_username  The username
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_by_username($_username)
	{
		$args =
		[
			'username' => $_username,
			'limit'         => 1
		];
		return self::get($args);
	}


	/**
	 * get all data by mobile
	 *
	 * @param      <type>  $_mobile  The mobile
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_by_mobile($_mobile)
	{
		$args =
		[
			'mobile' => $_mobile,
			'limit'       => 1
		];
		$result = self::get($args);
		return $result;
	}


	/**
	 * insert new recrod in users table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		return \dash\db\config::public_insert('users', ...func_get_args());
	}


	/**
	 * insert multi record in one query
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function insert_multi()
	{
		\dash\db\config::public_insert_multi('users', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * update field from users table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('users', ...func_get_args());
	}


	/**
	 * check valid ref
	 *
	 * @param      <type>  $_ref   The reference
	 */
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
	 * singup quice user
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function signup_quick($_args = [])
	{
		$_args      = array_merge(['datecreated' => date('Y-m-d H:i:s')], $_args);
		$insert_new = self::insert($_args);
		$insert_id  = \dash\db::insert_id();
		return $insert_id;
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
			// 'type'         => null,
			// 'title'        => null,
			// 'avatar'       => null,
			// 'status'       => null,
			// 'gender'       => null,
			// 'parent'       => null,
			// 'username'     => null,
			// 'pin'          => null,
			// 'twostep'      => null,
			// 'notification' => null,
			// 'unit_id'      => null,
			// 'language'     => null,
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
		self::$user_id = $insert_id;

		if(method_exists('\dash\utility\users', 'signup'))
		{
			$_args['insert_id'] = $insert_id;
			$_args['ref']       = $ref;
			\dash\utility\users::signup($_args);
		}
		return $insert_id;

	}


	/**
	 * Gets the count of users
	 * set $_type null to get all users by status and validstatus
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     <type>  The count.
	 */
	public static function get_count()
	{
		return \dash\db\config::public_get_count('users', ...func_get_args());
	}


	public static function permission_group()
	{
		$query = "SELECT COUNT(*) AS `count`, users.permission AS `permission` FROM users GROUP BY users.permission";
		return \dash\db::get($query, ['permission', 'count']);
	}


		/**
	 * get users method
	 *
	 * @param      <type>  $_fuck  The fuck
	 * @param      <type>  $_args  The arguments
	 */
	public static function __callStatic($_fn, $_args)
	{
		if(preg_match("/^(is|get|set)\_?(.*)$/", $_fn, $split))
		{
			if(isset($split[1]))
			{
				if(isset($_args[0]) && is_numeric($_args[0]))
				{
					if(!isset(self::$USERS_DETAIL[$_args[0]]))
					{
						self::$USERS_DETAIL[$_args[0]] = \dash\db\users::get_by_id($_args[0]);
					}
				}
				if($split[1] === 'get')
				{
					return self::static_get($split[2], ...$_args);
				}

				if($split[1] === 'set')
				{
					return self::static_set($split[2], ...$_args);
				}

				if($split[1] === 'is')
				{
					return self::static_is($split[2], ...$_args);
				}
			}
		}
	}


	/**
	 * get users data
	 *
	 * @param      <type>  $_field    The field
	 * @param      <type>  $_user_id  The user identifier
	 */
	private static function static_get($_field, $_user_id)
	{
		if($_field)
		{
			switch ($_field)
			{
				case null:
					if(isset(self::$USERS_DETAIL[$_user_id]))
					{
						return self::$USERS_DETAIL[$_user_id];
					}
					else
					{
						return false;
					}
					break;

				case 'language':
					if(isset(self::$USERS_DETAIL[$_user_id]['language']))
					{
						return self::$USERS_DETAIL[$_user_id]['language'];
					}
					else
					{
						return false;
					}
					break;

				case 'unit':
					if(isset(self::$USERS_DETAIL[$_user_id]['unit_id']))
					{
						$unit = \dash\app\units::get(self::$USERS_DETAIL[$_user_id]['unit_id']);
						if(isset($unit['title']))
						{
							return $unit['title'];
						}
					}
					return null;
					break;

				default:
					if(isset(self::$USERS_DETAIL[$_user_id][$_field]))
					{
						return self::$USERS_DETAIL[$_user_id][$_field];
					}
					else
					{
						return null;
					}
					break;
			}
		}
		else
		{
			if(isset(self::$USERS_DETAIL[$_user_id]))
			{
				return self::$USERS_DETAIL[$_user_id];
			}
			else
			{
				return null;
			}
		}
	}


	/**
	 * set users data
	 *
	 * @param      <type>  $_field    The field
	 * @param      <type>  $_user_id  The user identifier
	 */
	private static function static_set($_field, $_user_id, $_value = null)
	{
		$update = [];
		switch ($_field)
		{
			case 'language':
				if(\dash\language::check($_value))
				{
					$update['language'] = $_value;
				}
				break;

			case 'unit':
				$unit_id = \dash\app\units::get_id($_value);
				if($unit_id)
				{
					$update['unit_id'] = $unit_id;
				}
				break;

			case 'unit_id':
				$check = \dash\app\units::get($_value);
				if($check)
				{
					$update['unit_id'] = $_value;
				}
				break;

			default:
				$update[$_field] = $_value;
				break;
		}
		if(!empty($update))
		{
			\dash\db\users::update($update, $_user_id);
			unset(self::$USERS_DETAIL[$_user_id]);
		}
	}


	/**
	 * check some field by some value and return true or false
	 * @example self::is_guest(user_id) = false
	 *
	 * @param      <type>   $_field    The field
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private static function static_is($_field, $_user_id)
	{
		switch ($_field)
		{
			default:
			if(isset(self::$USERS_DETAIL[$_user_id][$_field]) && self::$USERS_DETAIL[$_user_id][$_field])
			{
				return true;
			}
			else
			{
				return false;
			}
			break;
		}
	}


	/**
	 * search in users to find the $_find in mobile or username or email
	 * if not exist
	 * search in contact to find it
	 * if finded retrun detail of this user
	 *
	 * @param      <type>  $_find  The find
	 */
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
