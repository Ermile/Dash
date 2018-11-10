<?php
namespace dash\db;

/** visitors managing **/
class visitors
{
	public static $fields =	" * ";

	public static function get_count($_where = [])
	{
		return \dash\db\config::public_get_count('visitors', $_where, \dash\db::get_db_log_name());
	}


	public static function url_get_count($_where = [])
	{
		return \dash\db\config::public_get_count('urls', $_where, \dash\db::get_db_log_name());
	}

	public static function get_url_like($_url, $_where = [])
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

		$query =
		"
			SELECT
				visitors.*,
				urls.pwd,
				urls.subdomain,
				urls.domain,
				urls.path
			FROM
				visitors
			LEFT JOIN urls ON urls.id = visitors.url_id
			WHERE urls.pwd LIKE '$_url'
			$where
		";
		$result = \dash\db::get($query, null, false, \dash\db::get_db_log_name());
		return $result;
	}

	public static function visitor_page($_url, $_where = [])
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

		$query =
		"
			SELECT COUNT(myTable.count) AS `myCount`
			FROM
			(
				SELECT
					COUNT(*) AS `count`,
					IF(visitors.user_id IS NULL, visitors.session_id , visitors.user_id) AS `myUser`
				FROM visitors
				LEFT JOIN urls ON urls.id = visitors.url_id
				WHERE
					urls.pwd = '$_url'
					$where
				GROUP BY myUser
			) AS `myTable`
		";

		$result = \dash\db::get($query, 'myCount', true, \dash\db::get_db_log_name());

		return $result;

	}


	private static function calc_start_time($_args)
	{
		if(isset($_args['period']))
		{
			$period = null;
			switch ($_args['period'])
			{
				case 'week':
					$period = date("Y-m-d H:i:s", strtotime('-1 week'));
					break;

				case 'month':
					$period = date("Y-m-d H:i:s", strtotime('-1 month'));
					break;

				case 'hours24':
				default:
					$period = date("Y-m-d H:i:s", strtotime('-24 hours'));
					break;
			}
			return $period;
		}
		return false;
	}


	public static function total_visit($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return null;
		}

		$query = "SELECT COUNT(*) AS `count` FROM visitors WHERE visitors.date >= '$start_time'";
		$result = \dash\db::get($query, 'count', true, \dash\db::get_db_log_name());
		return $result;
	}

	public static function total_visitor($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return null;
		}

		$query =
		"
			SELECT COUNT(myTable.count) AS `myCount`
			FROM
			(
				SELECT
				COUNT(*) AS `count`,
				IF(visitors.user_id IS NULL, visitors.session_id , visitors.user_id) AS `myUser`
				FROM visitors
				WHERE
					visitors.date >= '$start_time'
				GROUP BY myUser
			) AS `myTable`
		";

		$result = \dash\db::get($query, 'myCount', true, \dash\db::get_db_log_name());

		return $result;
	}

	public static function total_avgtime($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return null;
		}

		$query = "SELECT AVG(visitors.avgtime) AS `avg` FROM visitors WHERE visitors.avgtime IS NOT NULL AND visitors.date >= '$start_time' ";

		$result = \dash\db::get($query, 'avg', true, \dash\db::get_db_log_name());

		return $result;
	}


	public static function total_maxtrafictime($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return null;
		}

		$query =
		"
			SELECT DATE(visitors.date) AS `date`, HOUR(visitors.date) AS `hour`
			FROM  visitors
			WHERE visitors.date >= '$start_time'
			GROUP BY DATE(visitors.date), HOUR(visitors.date)
			ORDER BY COUNT(*) DESC
			LIMIT 1
		";

		$result = \dash\db::get($query, null, true, \dash\db::get_db_log_name());
		if(isset($result['date']) && isset($result['hour']))
		{
			return $result['date']. ' '. $result['hour'];
		}
		return null;
	}


	public static function chart_visitorchart($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return null;
		}

		if(!isset($_args['period']))
		{
			return false;
		}

		$subdomain = null;
		$join_url = null;
		if(isset($_args['subdomain']) && $_args['subdomain'])
		{
			$subdomain = " AND urls.subdomain = '$_args[subdomain]' ";
			$join_url = " LEFT JOIN urls ON urls.id = visitors.url_id ";
		}

		$diff_query = "HOUR(visitors.date) AS `myDate`,";
		switch ($_args['period'])
		{
			case 'week':
				$diff_query = "DATE(visitors.date) AS `myDate`,";
				break;

			case 'month':
				$diff_query = "DATE(visitors.date) AS `myDate`,";
				break;

			case 'hours24':
			default:
				$diff_query = "HOUR(visitors.date) AS `myDate`,";
				break;
		}

		$query =
		"
			SELECT COUNT(myTable.count) AS `myCount`, myTable.myDate
			FROM
				(
					SELECT
					$diff_query
					COUNT(DISTINCT visitors.id) AS `count`,
					IF(visitors.user_id IS NULL, visitors.session_id , visitors.user_id) AS `myUser`
					FROM visitors
					$join_url
					WHERE
						visitors.date >= '$start_time'
						$subdomain
					GROUP BY myDate, myUser
				) AS `myTable`
			GROUP BY myTable.myDate
		";

		$result_visitor = \dash\db::get($query, ['myDate', 'myCount'], false, \dash\db::get_db_log_name());


		$query =
		"
			SELECT
			$diff_query
			COUNT(DISTINCT visitors.id) AS `count`
			FROM visitors
			$join_url
			WHERE
				visitors.date >= '$start_time'
				$subdomain
			GROUP BY myDate
		";

		$result_visit = \dash\db::get($query, ['myDate', 'count'], false, \dash\db::get_db_log_name());

		return ['visitor' => $result_visitor, 'visit' => $result_visit];

	}

	public static function chart_country($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return false;
		}
		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				visitors.country AS `country`
			FROM
				visitors
			WHERE
				visitors.date >= '$start_time'
			GROUP BY visitors.country
		";
		$result = \dash\db::get($query, ['country', 'count'], false, \dash\db::get_db_log_name());
		return $result;

	}


	public static function chart_browser($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return false;
		}
		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				agents.name AS `name`
			FROM
				visitors
			LEFT JOIN agents ON agents.id = visitors.agent_id
			WHERE
				visitors.date >= '$start_time'
			GROUP BY agents.name
		";
		$result = \dash\db::get($query, ['name', 'count'], false, \dash\db::get_db_log_name());

		return $result;
	}


	public static function chart_os($_args)
	{
		$start_time = self::calc_start_time($_args);
		if(!$start_time)
		{
			return false;
		}
		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				agents.os AS `os`
			FROM
				visitors
			LEFT JOIN agents ON agents.id = visitors.agent_id
			WHERE
				visitors.date >= '$start_time'
			GROUP BY agents.os
		";
		$result = \dash\db::get($query, ['os', 'count'], false, \dash\db::get_db_log_name());
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
			// default return of this function 10 last record of search
			$_options['get_last'] = true;
		}

		$db_name = db_name;

		$default_options =
		[
			// just return the count record
			"get_count"         => false,
			// enable|disable paignation,
			"pagenation"        => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 10
			// set the limit    = null and pagenation = false to get all record whitout limit
			"limit"             => 10,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"       => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"         => 10,
			// the the last record inserted to post table
			"get_last"          => false,
			// default order by DESC you can change to DESC
			"order"             => "DESC",
			"order_rand"        => false,
			"order_raw"         => null,
			// custom sort by field
			"sort"              => null,
			"search_field"      => null,
			"public_show_field" =>
			"
				$db_name.users.displayname,
				$db_name.users.mobile,
				$db_name.users.avatar,
				urls.*,
				agents.*,
				visitors.*,
				referer.path AS `ref_url`,
				referer.pwd AS `ref_pwd`,
				referer.domain AS `ref_domain`
			",
			"master_join"       =>
			"
				LEFT JOIN agents ON visitors.agent_id = agents.id
				LEFT JOIN $db_name.users ON $db_name.users.id = visitors.user_id
				LEFT JOIN urls ON urls.id = visitors.url_id
				LEFT JOIN urls AS `referer` ON referer.id = visitors.url_idreferer
			",
		];

		// if limit not set and the pagenation is false
		// remove limit from query to load add record
		if(!isset($_options['limit']) && array_key_exists('pagenation', $_options) && $_options['pagenation'] === false)
		{
			$default_options['limit'] = null;
			$default_options['end_limit'] = null;
		}

		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		$master_join = null;
		if($_options['master_join'])
		{
			$master_join = $_options['master_join'];
		}

		if($_options['order'] && !in_array(mb_strtolower($_options['order']), ['asc', 'desc']))
		{
			$_options['order'] = 'DESC';
		}

		// ------------------ get count
		$only_one_value = false;
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
			$public_fields  = " COUNT(*) AS 'searchcount' FROM	`visitors` $master_join";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			if($_options['public_show_field'])
			{
				$public_show_field = $_options['public_show_field'];
			}
			else
			{
				$public_show_field = " * ";
			}

			$public_fields = " $public_show_field FROM `visitors` $master_join";

			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}


		if($_options['sort'])
		{
			$temp_sort = null;
			switch ($_options['sort'])
			{
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
				$order = " ORDER BY `visitors`.`id` DESC ";
			}
		}
		elseif($_options['order_rand'])
		{
			$order = " ORDER BY RAND() ";
		}
		else
		{
			if($_options['sort'])
			{
				if(!preg_match("/\./", $_options['sort']))
				{
					$order = " ORDER BY `$_options[sort]` $_options[order] ";
				}
				else
				{
					$order = " ORDER BY $_options[sort] $_options[order] ";
				}
			}
			else
			{
				$order = " ORDER BY `visitors`.`id` $_options[order] ";
			}
		}

		if(isset($_options['order_raw']) && $_options['order_raw'])
		{
			$order = " ORDER BY   $_options[order_raw] ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		$no_limit = false;
		if($_options['limit'] === false)
		{
			$no_limit = true;
		}

		$search_field = " ( visitors.caller LIKE '%__string__%' OR visitors.subdomain LIKE '%__string__%')";


		unset($_options['pagenation']);
		unset($_options['search_field']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);
		unset($_options['public_show_field']);
		unset($_options['master_join']);
		unset($_options['order_raw']);

		foreach ($_options as $key => $value)
		{
			if(!preg_match("/\./", $key))
			{
				$fkey = " `$key` ";
			}
			else
			{
				$fkey = " $key ";
			}

			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "search.`field` LIKE '%valud%'"
					$where[] = " $fkey $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " $fkey IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " $fkey = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " $fkey = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string !== null && $search_field && !is_array($_string))
		{
			$_string = trim($_string);

			$search = str_replace('__string__', $_string, $search_field);
			// "($search_field LIKE '%$_string%' )";

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
			$pagenation_query = "SELECT	COUNT(*) AS `count`	FROM `visitors` $master_join	$where $search ";
			$pagenation_query = \dash\db::get($pagenation_query, 'count', true, \dash\db::get_db_log_name());

			list($limit_start, $limit) = \dash\db::pagnation((int) $pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $limit ";
			}
		}

		$json = json_encode(func_get_args());
		if($no_limit)
		{
			$limit = null;
		}

		$query = "SELECT $public_fields $where $search $order $limit";

		if(!$only_one_value)
		{
			$result = \dash\db::get($query, null, false, \dash\db::get_db_log_name());
			$result = \dash\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \dash\db::get($query, 'searchcount', true, \dash\db::get_db_log_name());
		}

		return $result;
	}


}
?>
