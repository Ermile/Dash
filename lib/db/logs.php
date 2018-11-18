<?php
namespace dash\db;

/** logs managing **/
class logs
{
	private static $logUpdate = [];

	public static function save_temp_update()
	{
		if(empty(self::$logUpdate) || !is_array(self::$logUpdate))
		{
			return;
		}

		$query = [];

		foreach (self::$logUpdate as $key => $value)
		{
			$set = \dash\db\config::make_set($value['set']);
			if($set)
			{
				$query[] = " UPDATE logs SET $set WHERE logs.id = $value[id] LIMIT 1 ";
			}
		}

		self::$logUpdate = [];

		if(!empty($query))
		{
			return \dash\db::query(implode(';', $query), \dash\db::get_db_log_name(), ['multi_query' => true]);
		}
	}


	public static function update_temp($_set, $_id)
	{
		self::$logUpdate[] = ['set' => $_set, 'id' => $_id];
	}


	public static function get_count($_where = null)
	{
		return \dash\db\config::public_get_count('logs', $_where, \dash\db::get_db_log_name());
	}


	public static function get_caller_group()
	{
		$query = "SELECT count(*) AS `count`, logs.caller AS `caller` FROM logs GROUP BY logs.caller ORDER BY count(*) DESC";
		$resutl = \dash\db::get($query, ['caller', 'count'], false, \dash\db::get_db_log_name());
		return $resutl;
	}
	/**
	 * this library work with logs table
	 * v1.0
	 */

	public static $fields =	" * ";


	public static function multi_insert($_args)
	{
		$resutl = \dash\db\config::public_multi_insert('logs', $_args, \dash\db::get_db_log_name());
		if(\dash\db::get_db_log_name() === true)
		{
			$resutl = \dash\db::insert_id();
		}
		elseif(isset(\dash\db::$link_open[\dash\db::get_db_log_name()]))
		{
			$resutl = \dash\db::insert_id(\dash\db::$link_open[\dash\db::get_db_log_name()]);
		}
		return $resutl;
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

		$set = \dash\db\config::make_set($_args, ['type' => 'insert']);
		if($set)
		{
			$query  ="INSERT INTO logs SET $set ";

			$resutl = \dash\db::query($query, \dash\db::get_db_log_name());
			// get the link
			if(\dash\db::get_db_log_name() === true)
			{
				$resutl = \dash\db::insert_id();
			}
			elseif(isset(\dash\db::$link_open[\dash\db::get_db_log_name()]))
			{
				$resutl = \dash\db::insert_id(\dash\db::$link_open[\dash\db::get_db_log_name()]);
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
			return \dash\db::query($query, \dash\db::get_db_log_name());
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
		return \dash\db::query($query, \dash\db::get_db_log_name());
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

		if($_options['data'] && mb_strlen($_options['data']) >= 200)
		{
			$_options['data'] = substr($_options['data'], 0, 198);
		}

		if($_caller && mb_strlen($_caller) >= 200)
		{
			$_caller = substr($_caller, 0, 198);
		}

		$insert_log =
		[
			'caller'      => $_caller,
			'user_id'     => $user_id,
			'datecreated' => date("Y-m-d H:i:s"),
			'subdomain'   => \dash\url::subdomain() ? \dash\url::subdomain() : null,
			'visitor_id'  => $_options['visitor_id'],
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
	public static function get($_args, $_options = [])
	{
		if(isset($_options['join_user']))
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
				'db_name' => \dash\db::get_db_log_name(),
			];


		}
		else
		{
			$default =
			[
				'db_name' => \dash\db::get_db_log_name(),
			];

		}

		$_options            = array_merge($default, $_options);

		unset($_options['join_user']);

		return \dash\db\config::public_get('logs', $_args, $_options);
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
			'db_name' => \dash\db::get_db_log_name(),
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
		return \dash\db::get($query, null, true, \dash\db::get_db_log_name());
	}
}
?>
