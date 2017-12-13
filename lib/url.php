<?php
namespace lib;
/**
 * this lib handle url of our PHP framework, Dash
 * v 0.1
 */
class url
{
	// declare variables
	private static $url        = [];
	private static $real_url   = [];
	private static $uri        = [];
	private static $split_host = [];
	private static $path_split = [];
	private static $host_is_ip = false;
	/**
	 * initialize url and detect them
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		self::$url        = [];

		$host = self::server('HTTP_HOST');

		// ipv4
		if(preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $host))
		{
			self::$host_is_ip = true;
		}

		self::$split_host = explode('.', $host);

		self::$uri = self::server('REQUEST_URI');

		self::$uri = preg_replace("/^\//", '', self::$uri);

		self::_query();

		$path_raw   = str_replace('?'. self::$url['query'], '', self::$uri);

		self::$path_split = explode('/', $path_raw);

		self::_protocol();
		self::_port();
		self::_tld();
		self::_subdomain();
		self::_root();
		self::_domain();
		self::_host();
		self::_base();
		self::_path();
		self::_lang();
		self::_dir();
		self::_content();
		self::_property();
		self::_full();
	}

	/**
	 * get and ser the protocol
	 * http
	 * https
	 * or other protocol in HTTP_X_FORWARDED_PROTO
	 */
	private static function _protocol()
	{
		$protocol = 'http';
		if((self::server('HTTPS') && self::server('HTTPS') !== 'off') || self::server('SERVER_PORT') == 443)
		{
			$protocol = 'https';
		}
		elseif(self::server('HTTP_X_FORWARDED_PROTO'))
		{
			$protocol = self::server('HTTP_X_FORWARDED_PROTO');
		}

		self::$url['protocol']  = $protocol;
	}

	/**
	 * set the intval of port
	 */
	private static function _port()
	{
		$port = intval(self::server('SERVER_PORT'));
		self::$url['port'] = $port;
	}


	private static function _tld()
	{
		self::$url['tld'] = null;
		if(!self::$host_is_ip)
		{
			self::$url['tld'] = end(self::$split_host);
		}
	}


	private static function _subdomain()
	{
		$subdomain = null;
		if(count(self::$split_host) >= 3 && !self::$host_is_ip)
		{
			$subdomain = (isset(self::$split_host[0])) ? self::$split_host[0] : null;
		}
		self::$url['subdomain'] = $subdomain;
	}


	private static function _root()
	{
		$temp = self::$split_host;

		if(self::$url['tld'])
		{
			array_pop($temp);
		}

		if(self::$url['subdomain'])
		{
			array_shift($temp);
		}

		self::$url['root'] = implode('.', $temp);
	}


	private static function _domain()
	{
		self::$url['domain'] = self::$url['root']. '.'. self::$url['tld'];
	}


	private static function _host()
	{

		$host = null;
		if(self::$url['subdomain'])
		{
			$host .= self::$url['subdomain']. '.';
		}

		$host .= self::$url['domain'];

		if(self::$url['port'] !== 80)
		{
			$host .= ':'. self::$url['port'];
		}

		self::$url['host'] = $host;
	}


	private static function _base()
	{
		self::$url['base'] = self::$url['protocol'] . '://'. self::$url['host'];
	}


	private static function _full()
	{
		if(self::$uri)
		{
			self::$url['full'] = self::$url['base']. '/'. self::$uri;
		}
		else
		{
			self::$url['full'] = self::$url['base'];
		}
	}


	private static function _query()
	{
		$query  = self::server('QUERY_STRING');
		self::$url['query'] = null;
		if($query)
		{
			self::$url['query'] = $query;
		}
	}

	private static function _path()
	{
		self::$url['path'] = self::$uri;
	}

	private static function _lang()
	{
		self::$url['lang'] = null;
		if(array_key_exists(0, self::$path_split) && in_array(self::$path_split[0], ['fa', 'en', 'ar']))
		{
			self::$url['lang'] = self::$path_split[0];
			unset(self::$path_split[0]);
			self::$path_split = array_values(self::$path_split);
		}
	}


	private static function _content()
	{
		self::$url['content'] = null;
		if(array_key_exists(0, self::$path_split))
		{
			self::$url['content'] = self::$path_split[0];
		}
	}

	private static function _dir()
	{
		self::$url['dir']     = [];

		foreach (self::$path_split as $key => $value)
		{
			if($value != '')
			{
				self::$url['dir'][] = $value;
			}
		}
	}

	private static function _property()
	{
		self::$url['property']     = [];

		foreach (self::$path_split as $key => $value)
		{
			if(strpos($value, '=') !== false)
			{
				$tmp_split = explode('=', $value);
				if(count($tmp_split) === 2)
				{
					self::$url['property'][$tmp_split[0]] = $tmp_split[1];
				}
			}
		}
	}


	private static function server($_key = null)
	{
		$server = $_SERVER;

		if($_key)
		{
			if(array_key_exists($_key, $server))
			{
				return $server[$_key];
			}
			return null;
		}
		else
		{
			return $server;
		}
	}


	/**
	 * get value from url variable
	 * @param  [type] $_key [description]
	 * @return [type]       [description]
	 */
	public static function get($_key = null)
	{
		if($_key === null)
		{
			return self::$url;
		}
		else
		{
			if(array_key_exists($_key, self::$url))
			{
				return self::$url[$_key];
			}
			else
			{
				return null;
			}
		}
	}


	public static function get_real($_key = null)
	{
		$my_url = self::$real_url;

		if(empty($my_url))
		{
			$my_url = self::$url;
		}

		if($_key === null)
		{
			return $my_url;
		}
		else
		{
			if(array_key_exists($_key, $my_url))
			{
				return $my_url[$_key];
			}
			else
			{
				return null;
			}
		}
	}

	/**
	 * set key and value into array
	 * @param [type] $_key   [description]
	 * @param [type] $_value [description]
	 */
	public static function set($_key, $_value)
	{
		// create duplicate of self::$url as real_url
		if(empty(self::$real_url))
		{
			self::$real_url = self::$url;
		}

		self::$url[$_key] = $_value;
	}

	/**
	 * call every url function if exist
	 *
	 * @param      <type>  $_func  The function
	 * @param      <type>  $_args  The arguments
	 */
	public static function __callStatic($_func, $_args)
	{
		if(array_key_exists($_func, self::$url))
		{
			return self::$url[$_func];
		}
		// if cant find this url as function
		return null;
	}

}
?>