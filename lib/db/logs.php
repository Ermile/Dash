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

	public static $fields =
	"
			logs.id          	AS 	`id`,
			logs.logitem_id  	AS 	`logitem_id`,
			logitems.type    	AS 	`type`,
			logitems.caller  	AS 	`caller`,
			logitems.title   	AS 	`title`,
			logitems.priority	AS 	`priority`,
			logs.user_id     	AS 	`user_id`,
			logs.data        	AS 	`data`,
			logs.meta        	AS 	`meta`,
			logs.status      	AS 	`status`,
			logs.datecreated 	AS 	`createdate`,
			logs.datemodified	AS 	`datemodified`
		FROM
			logs
		LEFT JOIN logitems ON logitems.id = logs.logitem_id
	";

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

		if($_options['datalink'] && mb_strlen($_options['datalink']) >= 100)
		{
			$_options['datalink'] = substr($_options['datalink'], 0, 98);
		}

		if($_options['data'] && mb_strlen($_options['data']) >= 200)
		{
			$_options['data'] = substr($_options['data'], 0, 198);
		}

		if($_caller && mb_strlen($_caller) >= 200)
		{
			$_caller = substr($_caller, 0, 198);
		}

		$caller = [];
		$caller[] = \dash\url::content() ? \dash\url::content() : 'site';

		if(\dash\url::module())
		{
			$caller[] = \dash\url::module();
		}
		else
		{
			$caller[] = 'home';
		}

		if(\dash\url::child())
		{
			$caller[] = \dash\url::child();
		}

		$caller = implode(':', $caller);

		$caller = $caller. ';'. $_caller;

		$insert_log =
		[
			'caller'      => $caller,
			'user_id'     => $user_id,
			'datecreated' => date("Y-m-d H:i:s"),
			'subdomain'   => \dash\url::subdomain() ? \dash\url::subdomain() : null,
			'visitor_id'  => $_options['visitor_id'],
			'datalink'    => $_options['datalink'],
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
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of poll
			$_options['get_last'] = true;
		}

		$default_options =
		[
			// just return the count record
			"get_count"      => false,
			// enable|disable paignation,
			"pagenation"     => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit = null and pagenation = false to get all record whitout limit
			"limit"          => 15,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"    => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"      => 10,
			// the the last record inserted to post table
			"get_last"       => false,
			// default order by DESC you can change to DESC
			"order"          => "DESC",
			// custom sort by field
			"sort"           => null,
			// search in caller
			"caller"         => null,
			// search by user
			"user"           => null,
			// search by mobile
			"mobile"         => null,
			// search by date
			"date"           => null,
		];

		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		// ------------------ get count
		$only_one_value = false;
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
			$public_fields  =
			" COUNT(logs.id) AS 'logcount' FROM
				logs
			LEFT JOIN logitems ON logitems.id = logs.logitem_id";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = self::$fields;

			$domain = \dash\url::root();
			$domain = explode('.', $domain);
			if(count($domain) === 2 && isset($domain[0]))
			{
				if($domain[0] === 'sarshomar')
				{
					$log_have_desc = true;
					$public_fields = " logs.desc AS `desc`, ". $public_fields;
				}
			}

			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}

		if(isset($_options['caller']) && $_options['caller'])
		{
			if(preg_match("/\%/", $_options['caller']))
			{
				$where[] = " logitems.caller LIKE '$_options[caller]' ";

			}
			else
			{
				$where[] = " logitems.caller = '$_options[caller]' ";
			}
		}

		if(isset($_options['user']) && $_options['user'])
		{
			$where[] = " logs.user_id = $_options[user] ";
		}

		if(isset($_options['date']) && $_options['date'])
		{
			if(mb_strlen($_options['date']) === 8)
			{
				$where[] = " DATE(logs.datecreated) = DATE('$_options[date]') ";
			}
			else
			{
				$where[] = " TIME(logs.datecreated) = TIME('$_options[date]') ";
			}
		}

		if($_options['sort'])
		{
			$temp_sort = null;
			switch ($_options['sort'])
			{
				case 'type':
				case 'title':
				case 'meta':
				case 'priority':
					$temp_sort = 'logitems.'.  $_options['sort'];
					break;

				case 'count':
					$temp_sort = 'logitems.'.  $_options['sort'];
					break;

				case 'desc':
					if(isset($log_have_desc) && $log_have_desc)
					{
						$temp_sort = 'logs.desc';
					}
					else
					{
						$temp_sort = 'logitems.desc';
					}
					break;

				case 'date':
					$temp_sort = 'logs.datecreated';
					break;

				default:
					$temp_sort = $_options['sort'];
					break;
			}
			$_options['sort'] = $temp_sort;
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY logs.id DESC ";
			}
		}
		else
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY logs.id $_options[order] ";
			}
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];


		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);
		unset($_options['caller']);
		unset($_options['user']);
		unset($_options['date']);
		unset($_options['mobile']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "logs.`field` LIKE '%valud%'"
					$where[] = " logs.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " logs.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " logs.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " logs.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string !== null)
		{
			$search =
			"(
				logitems.type 		LIKE '%$_string%' OR
				logitems.caller 	LIKE '%$_string%' OR
				logitems.title 		LIKE '%$_string%' OR
				logs.data 				LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($where)
		{
			$where = "WHERE $where";
		}
		elseif($search)
		{
			$where = "WHERE";
		}

		if($pagenation && !$get_count)
		{
			$pagenation_query =
			"SELECT	COUNT(logs.id) AS `count`
			FROM
			logs
			LEFT JOIN logitems ON logitems.id = logs.logitem_id
			$where $search ";
			$pagenation_query = \dash\db::get($pagenation_query, 'count', true, self::get_db_log_name());

			list($limit_start, $limit) = \dash\db::pagnation((int) $pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$json = json_encode(func_get_args());
		$query = " SELECT $public_fields $where $search $order $limit";

		if(!$only_one_value)
		{
			$result = \dash\db::get($query, null, false, self::get_db_log_name());
			$result = \dash\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \dash\db::get($query, 'logcount', true, self::get_db_log_name());
		}

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
