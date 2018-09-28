<?php
namespace dash\db;

/** logs managing **/
class logs
{
	/**
	 * Gets the database name.
	 * if defined db_log return the db_log name to connect to this database
	 * else return true to connect to default database
	 *
	 * @return     boolean  The database name.
	 */
	public static function get_db_log_name()
	{
		return \dash\db\logitems::get_db_log_name();
	}

	/**
	 * this library work with logs table
	 * v1.0
	 */

	public static $fields =	" * ";


	public static function multi_insert($_args)
	{
		return \dash\db\config::public_multi_insert('logs', $_args, \dash\db::get_db_log_name());
	}


	public static function update_where()
	{
		return \dash\db\config::public_update_where('logs', ...func_get_args());
	}

	/**
	 * insert new recrod in logs table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		$set = \dash\db\config::make_set($_args);
		if($set)
		{
			$query  ="INSERT INTO logs SET $set ";

			$resutl = \dash\db::query($query, self::get_db_log_name());
			// get the link
			if(self::get_db_log_name() === true)
			{
				$resutl = \dash\db::insert_id();
			}
			elseif(isset(\dash\db::$link_open[self::get_db_log_name()]))
			{
				$resutl = \dash\db::insert_id(\dash\db::$link_open[self::get_db_log_name()]);
			}
			return $resutl;
		}
	}


	/**
	 * update field from logs table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{
		$set  = \dash\db\config::make_set($_args);
		if($set)
		{
			// make update query
			$query = "UPDATE logs SET $set WHERE logs.id = $_id";
			return \dash\db::query($query, self::get_db_log_name());
		}
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		// get id
		$query = "UPDATE FROM logs SET logs.notification_status = 'expire' WHERE logs.id = $_id ";
		return \dash\db::query($query, self::get_db_log_name());
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_caller   The caller
	 * @param      array   $_options  The options
	 */
	public static function set($_caller, $_user_id = null, $_options = [])
	{
		$default_options =
		[
			'visitor_id' => \dash\utility\visitor::id(),
			'status'     => 'enable',
			'data'       => null,
			'datalink'   => null,
			'meta'       => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if($_options['meta'])
		{
			$_options['meta'] = \dash\safe::safe($_options['meta']);

			if(is_array($_options['meta']) || is_object($_options['meta']))
			{
				$_options['meta'] = json_encode($_options['meta'], JSON_UNESCAPED_UNICODE);
			}
		}
		else
		{
			$_options['meta'] = null;
		}

		$user_id = null;

		if($_user_id && is_numeric($_user_id))
		{
			$user_id = $_user_id;
		}
		elseif(\dash\user::id())
		{
			$user_id = \dash\user::id();
		}

		// if($_options['datalink'] && mb_strlen($_options['datalink']) >= 100)
		// {
		// 	$_options['datalink'] = substr($_options['datalink'], 0, 98);
		// }

		if($_options['data'] && mb_strlen($_options['data']) >= 200)
		{
			$_options['data'] = substr($_options['data'], 0, 198);
		}

		if($_caller && mb_strlen($_caller) >= 200)
		{
			$_caller = substr($_caller, 0, 198);
		}

		// $caller = [];
		// $caller[] = \dash\url::content() ? \dash\url::content() : 'site';

		// if(\dash\url::module())
		// {
		// 	$caller[] = \dash\url::module();
		// }

		// if(\dash\url::child())
		// {
		// 	$caller[] = \dash\url::child();
		// }

		// $caller = implode(':', $caller);

		// $caller = $caller. ';'. $_caller;

		$insert_log =
		[
			'caller'      => $_caller,
			'user_id'     => $user_id,
			'datecreated' => date("Y-m-d H:i:s"),
			'subdomain'   => \dash\url::subdomain() ? \dash\url::subdomain() : null,
			'visitor_id'  => $_options['visitor_id'],
			// 'datalink'    => $_options['datalink'],
			'data'        => $_options['data'],
			'status'      => $_options['status'],
			'meta'        => $_options['meta'],
		];

		return self::insert($insert_log);
	}


	/**
	 * get log
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		$only_one_recort = false;

		if(empty($_args) || !is_array($_args))
		{
			return false;
		}

		if(isset($_args['limit']))
		{
			if($_args['limit'] == 1)
			{
				$only_one_recort = true;
			}

			$limit = "LIMIT $_args[limit]" ;
			unset($_args['limit']);
		}
		else
		{
			$limit = null;
		}

		$where = \dash\db\config::make_where($_args);

		$query = " SELECT * FROM logs WHERE $where $limit ";

		$result = \dash\db::get($query, null, $only_one_recort, self::get_db_log_name());
		if(isset($result['meta']) && substr($result['meta'], 0, 1) == '{')
		{
			$result['meta'] = json_decode($result['meta'], true);
		}
		else
		{
			$result = \dash\utility\filter::meta_decode($result);
		}
		return $result;
	}



	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 */
	public static function search($_string = null, $_options = [])
	{
		$db_name = db_name;

		$default =
		[

			"public_show_field" =>
			"
				logs.*,

				$db_name.users.displayname,
				$db_name.users.mobile,
				$db_name.users.avatar

			",
			"master_join"       =>
			"
				LEFT JOIN $db_name.users ON $db_name.users.id = logs.user_id
			",
			"db_name" => \dash\db::get_db_log_name(),
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default, $_options);
		$result = \dash\db\config::public_search('logs', $_string, $_options);

		return $result;
	}


	public static function end_log($_condition = [])
	{
		$where = [];
		foreach ($_condition as $key => $value)
		{
			if(is_string($value))
			{
				$value = "'$value'";
			}
			$where[] = "$key = $value";
		}

		if(!empty($where))
		{
			$where = join(" AND " , $where);
			$where = "WHERE $where";
		}
		else
		{
			$where = "";
		}
		$query = "SELECT logitems.*, logs.* FROM logs
		INNER JOIN logitems ON logitems.id = logs.logitem_id
		$where
		ORDER BY logs.datecreated DESC LIMIT 0,1";
		return \dash\db::get($query, null, true, self::get_db_log_name());
	}
}
?>
