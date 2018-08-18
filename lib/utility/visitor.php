<?php
namespace dash\utility;

/** Visitor: handle visitor details **/
class visitor
{
	/**
	 * this library get visitor detail and do some work on it
	 * v1.2
	 */

	// declare private static variable to save options
	private static $visitor;
	private static $link;
	private static $result;
	private static $external;
	private static $saved_query;
	private static $saved_query_result;


	public static function id()
	{
		if(isset($_SESSION['last_visitor_id']))
		{
			return $_SESSION['last_visitor_id'];
		}
		return null;
	}


	/**
	 * save a visitor in database
	 * @return [type] [description]
	 */
	public static function save()
	{
		if(!defined('db_log_name'))
		{
			return;
		}

		if(\dash\temp::get('force_stop_visitor'))
		{
			return;
		}

		// create link to database
		$connect = self::createLink();

		if($connect)
		{
			// create a query string
			$query     = self::create_query();
			if($query)
			{
				// execute query and save result
				$result  = \dash\db::query($query, db_log_name);
				$_SESSION['last_visitor_id'] = \dash\db::insert_id(self::$link);
				// return resul
				return $result;
			}
		}
		// else we have problem in connection, fix it later
		// header("HTTP/1.1 200 OK");
		return $connect;
	}


	/**
	 * create link to database if not exist
	 * @param  boolean $_force [description]
	 * @return [type]          [description]
	 */
	private static function createLink($_force = false)
	{
		if(!self::$link || $_force)
		{
			// open database connection and create link
			if(!\dash\db::connect(db_log_name, false))
			{
				// cant connect to database
				return false;
			}
			// save link as global variable
			self::$link = \dash\db::$link;
			return true;
		}
		return true;
	}


	/**
	 * create final query string to add new record to visitors table
	 * @return [string] contain insert query string
	 */
	public static function create_query($_array = false)
	{
		// declare variables
		self::$visitor['visitor_ip']    = \dash\server::ip(true);
		self::$visitor['service_id']    = self::checkDetailExist('services', self::service(), 	'name');
		self::$visitor['url_id']        = self::checkDetailExist('urls',     self::url(true),	'urlmd5', 'url');
		self::$visitor['agent_id']      = self::checkDetailExist('agents',   self::agent(), 	'agent');
		self::$visitor['url_idreferer'] = self::checkDetailExist('urls',     self::referer(true), 	'urlmd5', 'referer');
		self::$visitor['user_id']       = \dash\user::id();
		self::$visitor['external']      = self::$external;
		self::$visitor['date']          = date('Y-m-d');
		self::$visitor['time']          = date('H:i:s');
		self::$visitor['timeraw']       = time();
		self::$visitor['year']          = date('Y');
		self::$visitor['month']         = date('m');
		self::$visitor['day']           = date('d');

		if($_array === true)
		{
			return self::$visitor;
		}

		// create query string
		$set = \dash\db\config::make_set(self::$visitor);
		if($set)
		{
			$query = "INSERT INTO visitors SET $set";
			// return query
			return $query;
		}
		return null;
	}


	/**
	 * get visitor data
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_type = null)
	{
		switch ($_type)
		{
			case 'agent':
				$return = self::checkDetailExist('agents',   self::agent(), 'agent');
				break;

			default:
				$return = self::create_query(true);
				break;
		}
		return $return;
	}


	/**
	 * check value exist in table if not add new one
	 * @param  [type] $_table name of table
	 * @param  [type] $_value value to check
	 * @return [type]         final id
	 */
	public static function checkDetailExist($_table, $_value, $_field = null, $_fn = null)
	{
		$cache_key = 'visitor_'. md5(json_encode($_value, true));

		if($_table !== 'urls')
		{
			if($cache = \dash\session::get($cache_key))
			{
				return $cache;
			}
		}
		// create link to database
		self::createLink();

		$default = 0;

		if($_table === 'services')
		{
			return $_value;
		}

		if(!$_value)
		{
			return null;
		}

		$query = "SELECT * FROM $_table WHERE $_field = '$_value' LIMIT 1";

		if($query == self::$saved_query)
		{
			$result = self::$saved_query_result;
		}
		else
		{
			self::$saved_query = $query;
			// run query and save result
			self::$saved_query_result = $result = \dash\db::get($query, null, true, db_log_name);
		}

		if(isset($result['id']))
		{
			if($_table !== 'urls')
			{
				\dash\session::set($cache_key, $result['id']);
			}
			return $result['id'];
		}

		// create insert query to add new record
		$query = null;

		if($_table === 'agents')
		{
			// self::agent()
			$is_bot                  = self::isBot();
			$agent                   = \dash\utility\browserDetection::browser_detection('full_assoc');
			$insert_agent            = [];
			$insert_agent['agent']   = $_value;
			$insert_agent['group']   = array_key_exists('browser_working', $agent)  ? $agent['browser_working'] 	: null;
			$insert_agent['name']    = array_key_exists('browser_name', $agent) 	? $agent['browser_name'] 		: null;
			$insert_agent['version'] = array_key_exists('browser_number', $agent)	? $agent['browser_number'] 		: null;
			$insert_agent['os']      = array_key_exists('os', $agent) 				? $agent['os'] 					: null;
			$insert_agent['osnum']   = array_key_exists('os_number', $agent) 		? $agent['os_number'] 			: null;
			$insert_agent['meta']    = json_encode($agent, true);
			$insert_agent['robot']   = $is_bot ? 1 : null;
			$set                     = \dash\db\config::make_set($insert_agent);
			$query                   = "INSERT INTO agents SET $set ";
		}
		elseif($_table === 'urls')
		{
			$insert_url           = [];

			if($_fn === 'referer')
			{
				$url                  = self::referer(false);

				if(!$url)
				{
					return null;
				}

				$url                  = urldecode($url);
				$insert_url['domain'] = addslashes(parse_url($url, PHP_URL_SCHEME). '://'. parse_url($url, PHP_URL_HOST));
				$insert_url['query']  = strpos($url, '?') ? addslashes(substr($url, strpos($url, '?'))) : null;
				$insert_url['url']    = addslashes(strtok(str_replace(\dash\url::protocol(). '://'. $insert_url['domain'], '', $url), '?'));
				$insert_url['pwd']    = addslashes($url);
				$insert_url['urlmd5'] = addslashes(md5($url));
				$insert_url['host']   = addslashes(parse_url(urldecode($url), PHP_URL_HOST));
			}
			else
			{
				$insert_url['domain'] = addslashes(\dash\url::base());
				$insert_url['query']  = addslashes(\dash\url::query());
				$insert_url['url']    = addslashes(strtok(\dash\url::path(), '?'));
				$insert_url['pwd']    = addslashes(\dash\url::pwd());
				$insert_url['urlmd5'] = addslashes(self::url(true));
				$insert_url['host']   = addslashes(\dash\url::domain());
			}

			$set                  = \dash\db\config::make_set($insert_url);
			$query                = "INSERT INTO urls SET $set ";
		}
		elseif($_table === 'services')
		{
			$insert_service              = [];
			$insert_service['name']      = addslashes($_value ? $_value : \dash\url::domain());
			$insert_service['subdomain'] = addslashes(\dash\url::subdomain());
			$set                         = \dash\db\config::make_set($insert_service);
			$query                       = "INSERT INTO services SET $set ";
		}

		if($query)
		{
			\dash\db::query($query, db_log_name);

			$last_id = \dash\db::insert_id(self::$link);

			if($last_id)
			{
				return $last_id;
			}

		}
		// return default value
		return $default;
	}


	/**
	 * return current url
	 * @return [type] [description]
	 */
	public static function url($_md5 = false)
	{
		// $url = \dash\url::current();
		$url = \dash\url::pwd();
		if($_md5)
		{
			return md5($url);
		}

		$url = urlencode($url);
		return $url;
	}


	/**
	 * return current service id
	 * @return [type] [description]
	 */
	public static function service()
	{
		$domain = \dash\url::domain();

		$sub = null;

		if(\dash\url::subdomain())
		{
			$subdomain = \dash\url::subdomain();
			$sub = " AND subdomain = '$subdomain' ";
		}

		$cache_key = 'visitor_'. $domain;

		if(\dash\url::subdomain())
		{
			$cache_key .= '_'. \dash\url::subdomain();
		}

		if($cache = \dash\session::get($cache_key))
		{
			return $cache;
		}

		$query = "SELECT * FROM services WHERE name = '$domain' $sub LIMIT 1";

		$result  = \dash\db::get($query, null, true, db_log_name);
		// if has result return id
		if(isset($result['id']))
		{
			\dash\session::set($cache_key, $result['id']);
			return $result['id'];
		}
		else
		{
			$insert_service              = [];
			$insert_service['name']      = \dash\url::domain();
			$insert_service['subdomain'] = \dash\url::subdomain();
			$set                         = \dash\db\config::make_set($insert_service);
			$query                       = "INSERT INTO services SET $set ";

			\dash\db::query($query, db_log_name);

			$last_id = \dash\db::insert_id(self::$link);

			if($last_id)
			{
				return $last_id;
			}
		}

		return null;
	}




	/**
	 * return referer of visitor in current page
	 * @return [type] [description]
	 */
	public static function referer($_md5 = true)
	{
		$referer = null;
		if(isset($_SERVER['HTTP_REFERER']))
		{
			$referer = $_SERVER['HTTP_REFERER'];
		}
		$host_referer   = parse_url(urldecode($referer), PHP_URL_HOST);
		if($host_referer === $_SERVER['SERVER_NAME'])
		{
			self::$external = 0;
		}
		else
		{
			self::$external = 1;
		}

		if(!$referer)
		{
			return null;
		}

		if($_md5)
		{
			return md5($referer);
		}

		$referer = urlencode($referer);

		return $referer;
	}


	/**
	 * return agent of visitor in current page
	 * @return [type] [description]
	 */
	public static function agent($_encode = true)
	{
		$agent = null;
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$agent = $_SERVER['HTTP_USER_AGENT'];
		}
		// if user want encode referer
		if($_encode)
		{
			$agent = urlencode($agent);
		}
		return $agent;
	}


	/**
	 * check current user is bot or not
	 * @return boolean [description]
	 */
	public static function isBot()
	{
		$robot   = null;
		$agent   = self::agent();
		$botlist =
		[
			"Teoma",
			"alexa",
			"froogle",
			"Gigabot",
			"inktomi",
			"looksmart",
			"URL_Spider_SQL",
			"Firefly",
			"NationalDirectory",
			"Ask Jeeves",
			"TECNOSEEK",
			"InfoSeek",
			"WebFindBot",
			"girafabot",
			"crawler",
			"www.galaxy.com",
			"Googlebot",
			"Scooter",
			"Slurp",
			"msnbot",
			"appie",
			"FAST",
			"WebBug",
			"Spade",
			"ZyBorg",
			"rabaz",
			"Baiduspider",
			"Feedfetcher-Google",
			"TechnoratiSnoop",
			"Rankivabot",
			"Mediapartners-Google",
			"Sogou web spider",
			"WebAlta Crawler",
			"TweetmemeBot",
			"Butterfly",
			"Twitturls",
			"Me.dium",
			"Twiceler",
			"inoreader",
			"yoozBot",
		];

		foreach($botlist as $bot)
		{
			if(strpos($agent, $bot) !== false)
			{
				$robot = true;
			}
		}
		// return result
		return $robot;
	}


	/**
	 * Install visitor databases
	 * @return [type] [description]
	 */
	public static function install()
	{
		return \dash\db::execFolder('(core_name)_tools', 'visitor', true);
	}


	/**
	 * show visitor result
	 * @return [type] [description]
	 */
	public static function chart($_json = false)
	{
		if(!defined('db_log_name'))
		{
			return;
		}

		self::createLink();
		$service_id = self::service();
		/**
		 add getting unique visitor in next update
		 */

		$query =
		"
			SELECT
				visitors.date AS `date`,
				COUNT(*)       AS `humans`,
				COUNT(*)       AS `total`
			FROM
				`visitors`
			WHERE
				`service_id` = $service_id
			GROUP BY
				visitors.date
			ORDER BY
				visitors.date DESC
			LIMIT 10
		";

		$result = \dash\db::get($query, null, false, db_log_name);

		if(!$result)
		{
			return false;
		}

		$result = array_reverse($result);
		$temp = [];
		foreach ($result as $key => $value)
		{
			$date = $value['date'];
			if(\dash\data::lang_current() == 'fa')
			{
				$date = \dash\utility\jdate::date("Y/m/d", $value['date']);
			}

			$temp[] = ['key' => $date, 'value' => $value['total']];
		}
		if($_json)
		{
			$temp = json_encode($temp, JSON_UNESCAPED_UNICODE);
		}
		return $temp;
	}


	/**
	 * return top pages visited on this site
	 * @return [type] [description]
	 */
	public static function top_pages($_count = 10)
	{
		if(!defined('db_log_name'))
		{
			return;
		}

		self::createLink();
		$service_id = self::service();

		$query =
		"
			SELECT
				urls.url as url,
				count(visitors.id) as total
			FROM
				urls
			INNER JOIN visitors ON urls.id = visitors.url_id
			WHERE
				visitors.`service_id` = $service_id
			GROUP BY
				visitors.url_id
			ORDER BY
				total DESC
			LIMIT 0, $_count
		";


		$result  = \dash\db::get($query, null, false, db_log_name);

		if(!$result)
		{
			return false;
		}

		$temp = [];
		foreach ($result as $key => $value)
		{
			$url = urldecode($value['url']);
			if(strpos($result[$key]['url'], 'http://') !== false)
			{
				$url = substr($result[$key]['url'], 7);
			}

			$temp[] = ['key' => $url, 'value' => $value['total']];
		}

		$temp = json_encode($temp, JSON_UNESCAPED_UNICODE);
		return $temp;

	}
}
?>