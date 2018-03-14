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
		self::$url = [];

		$host      = self::server('HTTP_HOST');

		if(filter_var($host, FILTER_VALIDATE_IP))
		{
			self::$host_is_ip = true;
		}

		self::$split_host = explode('.', $host);

		self::$uri        = self::server('REQUEST_URI');

		if(substr(self::$uri, 0, 1) === '/')
		{
			self::$uri = substr(self::$uri, 1);
		}

		self::$url['query'] = self::_query();

		$path_raw           = str_replace('?'. self::$url['query'], '', self::$uri);

		self::$path_split   = explode('/', $path_raw);

		self::$url['protocol']  = self::_protocol();
		self::$url['port']      = self::_port();
		self::$url['tld']       = self::_tld();
		self::$url['subdomain'] = self::_subdomain();
		self::$url['root']      = self::_root();
		self::$url['domain']    = self::_domain();
		self::$url['host']      = self::_host();
		self::$url['base']      = self::_base();
		self::$url['path']      = self::_path();
		self::$url['lang']      = self::_lang();
		self::$url['dir']       = self::_dir();
		self::$url['content']   = self::_content();
		self::$url['property']  = self::_property();
		self::$url['full']      = self::_full();
		self::$url['full2']     = self::_full2();

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

		return $protocol;
	}


	/**
	 * set the intval of port
	 */
	private static function _port()
	{
		$port = intval(self::server('SERVER_PORT'));
		return $port;
	}


	private static function _tld()
	{
		$tld = null;
		if(!self::$host_is_ip)
		{
			$tld = end(self::$split_host);
		}
		return $tld;
	}


	private static function _subdomain()
	{
		$subdomain = null;
		if(count(self::$split_host) >= 3 && !self::$host_is_ip)
		{
			$subdomain = (isset(self::$split_host[0])) ? self::$split_host[0] : null;
		}
		return $subdomain;
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

		return implode('.', $temp);
	}


	private static function _domain()
	{
		return self::$url['root']. '.'. self::$url['tld'];
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

		return $host;
	}


	private static function _base()
	{
		return self::$url['protocol'] . '://'. self::$url['host'];
	}


	private static function _full()
	{
		$full = null;
		if(self::$uri)
		{
			$full = self::$url['base']. '/'. self::$uri;
		}
		else
		{
			$full = self::$url['base'];
		}
		return $full;
	}

	private static function _full2()
	{
		$full = null;
		if(self::$uri)
		{
			$full = self::$url['base']. '/'. self::$uri;
		}
		else
		{
			$full = self::$url['base'];
		}
		$full = str_replace('?'.self::_query(), '', $full);
		return $full;
	}


	private static function _query()
	{
		$query = null;
		if(self::server('QUERY_STRING'))
		{
			$query = self::server('QUERY_STRING');
		}
		return $query;
	}


	private static function _path()
	{
		return self::$uri;
	}


	private static function _lang()
	{
		$lang = null;

		if(array_key_exists(0, self::$path_split) && in_array(self::$path_split[0], ['fa', 'en', 'ar']))
		{
			$lang = self::$path_split[0];
			unset(self::$path_split[0]);
			self::$path_split = array_values(self::$path_split);
		}
		return $lang;
	}


	private static function _content()
	{
		$content = null;
		if(array_key_exists(0, self::$path_split))
		{
			if(\lib\content::is_content(self::$path_split[0]))
			{
				$content = self::$path_split[0];
			}
		}
		return $content;
	}


	private static function _dir()
	{
		$dir = [];

		foreach (self::$path_split as $key => $value)
		{
			if($value != '')
			{
				$dir[] = $value;
			}
		}
		return $dir;
	}


	private static function _property()
	{
		$property = [];

		foreach (self::$path_split as $key => $value)
		{
			if(strpos($value, '=') !== false)
			{
				$tmp_split = explode('=', $value);
				if(count($tmp_split) === 2)
				{
					$property[$tmp_split[0]] = $tmp_split[1];
				}
			}
		}
		return $property;
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
	public static function get($_key = null, $_real = false)
	{
		$my_url = self::$url;

		if($_real)
		{
			if(!empty(self::$real_url))
			{
				$my_url = self::$real_url;
			}
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
