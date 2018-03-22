<?php
namespace lib;
/**
 * this lib handle url of our PHP framework, Dash
 * v 3.2
 *
 * This lib detect all part of url and return each one seperate or combine some of them
 * Below example is the sample of this url lib
 *
 * example : http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes?id=5&page=8
 *
 *** get from $_SERVER
 * 'protocol'   => 'http'
 * 'host'       => 'ermile.jibres.com'					[subdomain+domain]	(HTTP_HOST)
 * 'port'       => 80														(SERVER_PORT)
 * 'query'      => 'id=5&page=8'											(QUERY_STRING)
 *
 * dont use uri directly in normal condition
 * 'uri'        => '/en/a/thirdparty/general/edit/test=yes?id=5&page=8'		(REQUEST_URI)
 *
 *
 *** calculated from above values
 * 'subdomain'  => 'ermile'
 * 'root'       => 'jibres'
 * 'tld'        => 'com'
 *
 * 'domain'     => 'jibres.com'							[root+tld+port]
 * 'base'       => 'http://ermile.jibres.com'			[protocol+host]
 * 'site'       => 'http://jibres.com'					[protocol+domain]
 * 'lang'       => 'en'
 * 'content'    => 'a'
 * 'module'     => 'thirdparty'
 * 'child'      => 'general'
 * 'subchild'   => 'edit'
 * 'prefix'     => '/en/a'								[lang+content]
 * 'dir'        => [ 0 => 'thirdparty', 1 => 'general', 2 => 'edit', 3=> 'test=yes']
 * 'directory'  => 'thirdparty/general/edit/test=yes'
 * 'path'       => 'en/a/thirdparty/general/edit/test=yes?id=5&page=8'
 * 'pwd'        => 'http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes?id=5&page=8'
 * 'current'    => 'http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes'
 * 'this' 		=> 'http://ermile.jibres.com/en/a/thirdparty'
 * 'here'       => 'http://ermile.jibres.com/en/a'
 */
class url
{
	// declare variables
	private static $url             = [];
	// the server request_uri
	private static $uri             = [];
	// split host in $_SERVER by '.'
	private static $split_host      = [];
	// split request_uri in $_SERVER by '/'
	private static $path_split      = [];
	private static $temp_path_split = [];


	/**
	 * initialize url and detect them
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		self::$url = [];

		// get base values from server
		self::$url['protocol'] = self::_protocol();
		self::$url['host']     = self::_host();
		self::$url['port']     = self::_port();
		self::$url['uri']      = self::_uri();
		self::$url['query']    = self::_query();

		// analyse host
		// self::$url = array_merge(self::$url, self::analyse_host(self::$url['host']));
		$analysed_host = self::analyse_host(self::$url['host']);
		self::$url['subdomain'] = $analysed_host['subdomain'];
		self::$url['root']      = $analysed_host['root'];
		self::$url['tld']       = $analysed_host['tld'];

		// generate with host and protocol
		self::$url['domain']    = self::_domain();
		self::$url['base']      = self::_base();
		self::$url['site']      = self::_site();



		self::$uri = self::server('REQUEST_URI');
		self::$uri = rtrim(self::$uri, '/');

		if(substr(self::$uri, 0, 1) === '/')
		{
			self::$uri = substr(self::$uri, 1);
		}

		self::$path_split       = explode('/', self::remove_query(self::$uri));
		self::$temp_path_split  = self::$path_split;


		self::$url['path']      = self::_path();
		self::$url['lang']      = self::_lang();
		self::$url['content']   = self::_content();
		self::$url['dir']       = self::_dir();
		self::$url['directory'] = self::_directory();
		self::$url['module']    = self::_module();
		self::$url['child']     = self::_child();
		self::$url['subchild']  = self::_subchild();
		self::$url['pwd']       = self::_pwd();
		self::$url['current']   = self::_current();
		self::$url['prefix']    = self::_prefix();
		self::$url['here']      = self::_here();
		self::$url['this']      = self::_this();

		var_dump(self::$url);
	}





	/**
	 * if we are in different address, return in
	 * @return string of another addr
	 */
	private static function _in_another_addr()
	{
		//
		if(isset($_SERVER['PHP_SELF']))
		{
			$php_self = $_SERVER['PHP_SELF'];
			$php_self = str_replace('/index.php', '', $php_self);
			if($php_self)
			{
				return $php_self;
			}
		}

		return null;
	}


	/**
	 * get site url
	 * @return string of site address
	 */
	private static function _site()
	{
		return self::$url['protocol']. '://'. self::$url['domain'];
	}


	/**
	 * get url base to used in tag or links
	 * @return sting of base
	 */
	private static function _base()
	{
		$my_base = self::$url['protocol'] . '://'. self::$url['host'];

		if(self::_in_another_addr())
		{
			$my_base .= self::_in_another_addr();
		}
		return $my_base;
	}


	/**
	 * calc domain address
	 * @return string of domain
	 */
	private static function _domain()
	{
		$domain = self::$url['root'];
		if(self::$url['tld'])
		{
			$domain .= '.'. self::$url['tld'];
		}
		if(self::$url['port'] === 80 || self::$url['port'] === 443)
		{
			// do nothing on default ports
		}
		else
		{
			$domain .= ':'. self::$url['port'];
		}

		if(self::_in_another_addr())
		{
			$domain .= self::_in_another_addr();
		}

		return $domain;
	}


	/**
	 * get host of server and return array contain 3part of it
	 * @param  sting $_host
	 * @return array of contain subdomain and root and tld
	 */
	private static function analyse_host($_host)
	{
		$my_host   = explode('.', $_host);
		$my_result = ['subdomain' => null, 'root' => null, 'tld' => null];

		// if host is ip, only set as root
		if(filter_var($_host, FILTER_VALIDATE_IP))
		{
			// something like 127.0.0.5
			$my_result['root'] = $_host;
		}
		elseif(count($my_host) === 1)
		{
			// something like localhost
			$my_result['root'] = $_host;
		}
		elseif(count($my_host) === 2)
		{
			// like jibres.com
			$my_result['root'] = $my_host[0];
			$my_result['tld']  = $my_host[1];
		}
		elseif(count($my_host) >= 3)
		{
			// some conditons like
			// ermile.ac.ir
			// ermile.jibres.com
			// ermile.jibres.ac.ir
			// a.ermile.jibres.ac.ir

			// get last one as tld
			$my_result['tld']  = end($my_host);
			array_pop($my_host);

			// check last one after remove is probably tld or not
			$known_tld    = ['com', 'org', 'net', 'gov', 'co', 'ac', 'id', 'sch', 'biz'];
			$probably_tld = end($my_host);
			if(in_array($probably_tld, $known_tld))
			{
				$my_result['tld'] = $probably_tld. '.'. $my_result['tld'];
				array_pop($my_host);
			}

			$my_result['root'] = end($my_host);
			array_pop($my_host);

			// all remain is subdomain
			if(count($my_host) > 0)
			{
				$my_result['subdomain'] = implode('.', $my_host);
			}
		}

		return $my_result;
	}


	/**
	 * get url parameter and query if exist
	 * @return string query
	 */
	private static function _query()
	{
		$query = null;
		if(self::server('QUERY_STRING'))
		{
			$query = self::server('QUERY_STRING');
		}
		return $query;
	}


	/**
	 * get uri from server detail
	 * @return string uri
	 */
	private static function _uri()
	{
		return self::server('REQUEST_URI');
	}


	/**
	 * set the number of port
	 * @return int port number
	 */
	private static function _port()
	{
		$port = intval(self::server('SERVER_PORT'));
		return $port;
	}

	/**
	 * get host from server detail
	 * @return string host
	 */
	private static function _host()
	{
		return self::server('HTTP_HOST');
	}


	/**
	 * get protocol contain http and https and support cdn and dns forward
	 * @return string used protocol
	 */
	private static function _protocol()
	{
		$protocol = 'http';
		if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || self::server('SERVER_PORT') == 443)
		{
			$protocol = 'https';
		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			$protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
		}

		return $protocol;
	}














	private static function _this()
	{
		return \lib\url::here(). '/'. \lib\url::module();
	}


	private static function _here()
	{
		$new_url = self::$url['base'];
		if(self::lang())
		{
			$new_url .= '/'. self::lang();
		}
		if($content = \lib\url::content())
		{
			$new_url .= '/'. $content;
		}
		return $new_url;
	}


	private static function _prefix()
	{
		$prefix = null;
		if($lang = \lib\url::lang())
		{
			$prefix .= $lang;
		}
		if($content = \lib\url::content())
		{
			$prefix .= '/'. $content;
		}
		$new_url = $prefix;

		if(substr($new_url, 0, 1) !== '/')
		{
			$new_url = '/'. $new_url;
		}

		return $new_url;
	}


	private static function _module()
	{
		return self::dir(0);
	}


	private static function _child()
	{
		return self::dir(1);
	}


	private static function _subchild()
	{
		return self::dir(2);
	}


	private static function _pwd()
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


	private static function _current()
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


	private static function _path()
	{
		return self::$uri;
	}


	private static function _lang()
	{
		$lang = null;

		if(array_key_exists(0, self::$path_split) && array_key_exists(self::$path_split[0], \lib\language::list()))
		{
			$lang = self::$path_split[0];
			unset(self::$path_split[0]);
			unset(self::$temp_path_split[0]);
			self::$path_split = array_values(self::$path_split);
			if(is_array(self::$temp_path_split))
			{
				self::$temp_path_split = array_values(self::$temp_path_split);
			}
		}
		return $lang;
	}


	private static function _content()
	{
		$content = null;
		if(array_key_exists(0, self::$path_split))
		{
			if(\lib\engine\content::load(self::$path_split[0]))
			{
				$content = self::$path_split[0];
				unset(self::$temp_path_split[0]);
				if(is_array(self::$temp_path_split))
				{
					self::$temp_path_split = array_values(self::$temp_path_split);
				}
			}
		}
		return $content;
	}


	private static function _dir()
	{
		$dir = [];

		foreach (self::$temp_path_split as $key => $value)
		{
			if($value != '')
			{
				$dir[] = $value;
			}
		}
		return $dir;
	}

	private static function _directory()
	{
		return implode(self::$temp_path_split, '/');
	}

	private static function remove_query($_uri)
	{
		return strtok($_uri, '?');
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
	 * return all values detected from url
	 * @return [type] [description]
	 */
	public static function all()
	{
		return self::$url;
	}


	/**
	 * check if we are in local return true
	 * @return boolean [description]
	 */
	public static function isLocal()
	{
		if(self::get('tld') === 'local')
		{
			return true;
		}

		return false;
	}


	/**
	 * return specefic dir or array of all
	 * @param  [type] $_index [description]
	 * @return [type]         [description]
	 */
	public static function dir($_index = null)
	{
		$my_dir = self::get('dir');
		if(is_numeric($_index))
		{
			if(is_array($my_dir))
			{
				if(isset($my_dir[$_index]))
				{
					return $my_dir[$_index];
				}
				else
				{
					return null;
				}
			}
		}
		else
		{
			return $my_dir;
		}

		return null;
	}


	/**
	 * call every url function if exist
	 *
	 * @param      <type>  $_func  The function
	 * @param      <type>  $_args  The arguments
	 */
	public static function __callStatic($_func, $_args = null)
	{
		if(array_key_exists($_func, self::$url))
		{
			$result = self::$url[$_func];
			if($_args)
			{
				if(isset($result[$_args]))
				{
					return $result[$_args];
				}
				else
				{
					return null;
				}
			}
			else
			{
				return $result;
			}

		}
		// if cant find this url as function
		return null;
	}



	public static function urlfilterer($_input, $_strip = true)
	{
		$_input = urldecode($_input);
		$_input = str_ireplace(array("\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', $_input);
		if($_strip)
		{
			$_input = strip_tags($_input);
		}
		$_input = htmlentities($_input, ENT_QUOTES, 'UTF-8'); // or whatever encoding you use...
		return trim($_input);
	}
}
?>
