<?php
namespace dash;

/** Create simple and clean connection to db **/
class db
{
	/**
	 * this library doing useful db actions
	 * v4.4
	 */
	use \lib\db\mysql\tools\connect;
	use \lib\db\mysql\tools\backup;
	use \lib\db\mysql\tools\install;
	use \lib\db\mysql\tools\get;
	use \lib\db\mysql\tools\info;
	use \lib\db\mysql\tools\pagination;
	use \lib\db\mysql\tools\log;


	/**
	 * run query string and return result
	 * now you don't need to check result
	 * @param  [type] $_qry [description]
	 * @return [type]       [description]
	 */
	public static function query($_qry, $_db_name = true, $_options = [])
	{
		$default_options =
		[
			// run mysqli_multi_query
			'multi_query'         => false,
			// default auto create database
			'auto_create_database' => false,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		// on default system connect to default db
		$different_db = false;

		// check connect to default db or no
		if($_db_name === true)
		{
			// connect to main database
			self::connect(true, $_options['auto_create_database']);
		}
		elseif(is_string($_db_name))
		{
			// connect to different db
			self::connect($_db_name, $_options['auto_create_database']);
			// different db used.
			$different_db = true;
		}
		else
		{
			return false;
		}

		// check the mysql link
		if(!self::$link)
		{
			return null;
		}

		// to fix: mysql server has gone away!
		if(!@mysqli_ping(self::$link))
		{
			return false;
		}

		// get time before execute query
		$qry_exec_time = microtime(true);
		/**
		 * send the query to mysql engine
		 */
		if($_options['multi_query'] === true)
		{
			$result = mysqli_multi_query(self::$link, $_qry);
			do
			{
				if ($r = mysqli_use_result(self::$link))
				{
					$r->close();
				}

				if (!mysqli_more_results(self::$link))
				{
					break;
				}

				mysqli_more_results(self::$link);
			}
			while (mysqli_next_result(self::$link));
		}
		else
		{
			$result = mysqli_query(self::$link, $_qry);
		}
		// get diff of time after exec
		$qry_exec_time = microtime(true) - $qry_exec_time;

		// if debug mod is true save all string query
		if(\lib\option::config('debug'))
		{
			self::log($_qry, $qry_exec_time);
		}
		// calc exex time in ms
		$qry_exec_time_ms = round($qry_exec_time*1000);
		// if spend more time, save it in special file
		if($qry_exec_time_ms > 1000)
		{
			self::log($_qry, $qry_exec_time, 'log-critical.sql');
		}
		elseif($qry_exec_time_ms > 500)
		{
			self::log($_qry, $qry_exec_time, 'log-warn.sql');
		}
		elseif($qry_exec_time_ms > 200)
		{
			self::log($_qry, $qry_exec_time, 'log-check.sql');
		}


		// check the mysql result
		if(!is_a($result, 'mysqli_result') && !$result)
		{
			// no result exist
			// save mysql error
			$temp_error = "#". date("Y-m-d H:i:s") . "\n$_qry\n/* ERROR\tMYSQL ERROR\n". mysqli_error(self::$link)." */";
			self::log($temp_error, $qry_exec_time, 'error.sql');
			return false;
		}

		// set the default link
		if($different_db)
		{
			self::$link = self::$link_default;
		}

		// return the mysql result
		return $result;
	}


	/**
	 * transaction
	 */
	public static function transaction($_db_name = true)
	{
		return self::query("START TRANSACTION", $_db_name);
	}


	/**
	 * commit
	 */
	public static function commit($_db_name = true)
	{
		return self::query("COMMIT", $_db_name);
	}


	/**
	 * rollback
	 */
	public static function rollback($_db_name = true)
	{
		return self::query("ROLLBACK", $_db_name);
	}
}
?>