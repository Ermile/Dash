<?php
namespace lib;
/**
 * this lib handle url of our PHP framework, Dash
 * v 1.0
 *
 * This lib detect all part of url and return each one seperate or combine some of them
 * Below example is the sample of this url lib
 *
 * example : http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes?id=5&page=8
 *
 * 'protocol'   => 'http'
 * 'subdomain'  => 'ermile'
 * 'root'       => 'jibres'
 * 'tld'        => 'com'
 * 'port'       => 80
 * 'domain'     => 'jibres.com'							[root+tld+port]
 * 'host'       => 'saeed.jibres.com'					[subdomain+domain]
 * 'base'       => 'http://ermile.jibres.com'			[protocol+host]
 * 'site'       => 'http://jibres.com'					[protocol+domain]
 * 'lang'       => 'en'
 * 'content'    => 'a'
 * 'module'     => 'thirdparty'
 * 'child'      => 'general'
 * 'subchild'   => 'edit'
 * 'query'      => 'id=5&page=8'
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
	// check url is ip [example: 127.0.0.2]
	private static $host_is_ip      = false;
	// save base to use in some function
	private static $base            = null;


	/**
	 * initialize url and detect them
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		self::$url = [];

		$host = self::server('HTTP_HOST');

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

		self::$url['query']     = self::_query();
		self::$path_split       = explode('/', self::remove_query(self::$uri));
		self::$temp_path_split  = self::$path_split;

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
		self::$url['content']   = self::_content();
		self::$url['dir']       = self::_dir();
		self::$url['directory'] = self::_directory();
		self::$url['module']    = self::_module();
		self::$url['child']     = self::_child();
		self::$url['subchild']  = self::_subchild();
		self::$url['pwd']       = self::_pwd();
		self::$url['current']   = self::_current();
		self::$url['prefix']    = self::_prefix();

		$base                   = null;
		$base                  .= \lib\url::protocol(). '://';
		$base                  .= \lib\url::host();
		if($lang = \lib\url::lang())
		{
			$base              .= '/'. $lang;
		}
		self::$base             = $base;

		self::$url['here']      = self::_here();
		self::$url['site']      = self::_site();
		self::$url['this']      = self::_this();
	}


	private static function _site()
	{
		$site = null;
		$site .= \lib\url::protocol(). '://';
		$site .= \lib\url::domain();
		return $site;
	}


	private static function _this()
	{
		return \lib\url::here(). '/'. \lib\url::module();
	}


	private static function _here()
	{
		$new_url = self::$base;
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
		$module = null;
		if(isset(self::$temp_path_split[0]))
		{
			$module = self::$temp_path_split[0];
			unset(self::$temp_path_split[0]);
			if(is_array(self::$temp_path_split))
			{
				self::$temp_path_split = array_values(self::$temp_path_split);
			}
		}
		return $module;
	}


	private static function _child()
	{
		$child = null;
		if(isset(self::$temp_path_split[0]))
		{
			$child = self::$temp_path_split[0];
			unset(self::$temp_path_split[0]);
			if(is_array(self::$temp_path_split))
			{
				self::$temp_path_split = array_values(self::$temp_path_split);
			}
		}
		return $child;
	}


	private static function _subchild()
	{
		$subchild = null;
		if(isset(self::$temp_path_split[0]))
		{
			$subchild = self::$temp_path_split[0];
			unset(self::$temp_path_split[0]);
			if(is_array(self::$temp_path_split))
			{
				self::$temp_path_split = array_values(self::$temp_path_split);
			}
		}
		return $subchild;
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
		$domain = self::$url['root']. '.'. self::$url['tld'];
		if(self::$url['port'] !== 80)
		{
			$domain .= ':'. self::$url['port'];
		}
		return $domain;
	}


	private static function _host()
	{
		$host = null;
		if(self::$url['subdomain'])
		{
			$host .= self::$url['subdomain']. '.';
		}

		$host .= self::$url['domain'];

		return $host;
	}


	private static function _base()
	{
		return self::$url['protocol'] . '://'. self::$url['host'];
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
			if(\lib\content::is_content(self::$path_split[0]))
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


	public static function isLocal()
	{
		if(self::get('tld') === 'local')
		{
			return true;
		}

		return false;
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
