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
	// use in old controller url syntax TO DO: remove it
	private static $base       = null;


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


		//--------------------------------------------------------------- CONTROLLER INDEX OF URL
		// --- controller url syntax
		$base = null;
        $base .= \lib\url::protocol(). '://';
        $base .= \lib\url::host();
        if($lang = \lib\url::lang())
    	{
    		$base .= '/'. $lang;
    	}
    	self::$base = $base;

		self::$url['base']         = self::_OLD_base(); // --------- duplicate index of array
		self::$url['baseContent']  = self::_baseContent();
		self::$url['baseFull']     = self::_baseFull();
		self::$url['baseRaw']      = self::_baseRaw();
		self::$url['prefix']       = self::_prefix();
		self::$url['sub']          = self::_subdomain(); // use new syntax; ! changed the name
		self::$url['path']         = self::_OLD_path(); // --------- duplicate index of array
		self::$url['breadcrumb']   = self::_breadcrumb();
		self::$url['param']        = self::_query(); // use new syntax; ! changed the name
		self::$url['domain']       = self::_root(); // use new syntax; ! changed the name | // --------- duplicate index of array
		self::$url['raw']          = self::_domain(); // use new syntax; ! changed the name
		self::$url['root']         = self::_base(); // use new syntax; ! changed the name
		self::$url['MainProtocol'] = self::_protocol(); // use new syntax; ! changed the name // --------- duplicate index of array
		self::$url['MainSite']     = self::_domain(); // use new syntax; ! changed the name
		self::$url['module']       = self::_module();
		self::$url['child']        = self::_child();
		self::$url['tags']         = self::_tags(); // is null !!!
		self::$url['cats']         = self::_cats(); // is null !!!
		self::$url['pages']        = self::_pages(); // is null !!!
		self::$url['LoginService'] = self::_LoginService();
		self::$url['account']      = self::_account();
		self::$url['MainService']  = self::_MainService();

		// example : http://saeed.jibres.local/a/thirdparty/edit?id=5&page=8
		// 'query'        => 'id=5&page=8'
		// 'protocol'     => 'http'
		// 'port'         => 80
		// 'tld'          => 'local'
		// 'subdomain'    => 'saeed'
		// 'root'         => 'http://saeed.jibres.local'
		// 'domain'       => 'jibres'
		// 'host'         => 'saeed.jibres.local'
		// 'base'         => 'http://saeed.jibres.local'
		// 'path'         => 'thirdparty/edit'
		// 'lang'         => null
		// 'dir'          =>
		// [
		// 	0 => 'a'
		// 	1 => 'thirdparty'
		// 	2 => 'edit'
		// ]
		// 'content'      => 'a'
		// 'property'     =>  []


		// 'full'         => 'http://saeed.jibres.local/a/thirdparty/edit?id=5&page=8'
		// 'full2'        => 'http://saeed.jibres.local/a/thirdparty/edit'
		// 'baseContent'  => 'http://saeed.jibres.local/a'
		// 'baseFull'     => 'http://saeed.jibres.local/a'
		// 'baseRaw'      => 'http://saeed.jibres.local/a'
		// 'prefix'       => '/a'
		// 'sub'          => 'saeed'
		// 'breadcrumb'   =>
		// [
		// 	1 => 'thirdparty'
		// 	2 => 'edit'
		// ]
		// 'param'        => 'id=5&page=8'
		// 'raw'          => 'jibres.local'
		// 'MainProtocol' => 'http'
		// 'MainSite'     => 'http://saeed.jibres.local.local'
		// 'module'       => 'thirdparty'
		// 'child'        => null
		// 'tags'         => null
		// 'cats'         => null
		// 'pages'        => null
		// 'LoginService' => 'http://jibres/account'
		// 'account'      => 'http://jibres/account'
		// 'MainService'  => 'http://ermile.local'
	}

	private static function _OLD_base()
	{
		return self::$base;
	}

	private static function _baseContent()
	{
		$base = null;
        $base .= \lib\url::protocol(). '://';
        $base .= \lib\url::host();
		$new_url = $base;
		if($content = \lib\url::content())
		{
			$new_url .= '/'. $content;
		}
		return $new_url;
	}

	private static function _baseFull()
	{
		$new_url = self::$base;
		if($content = \lib\url::content())
		{
			$new_url .= '/'. $content;
		}
		return $new_url;
	}

	private static function _baseRaw()
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
		return $new_url;
	}


	private static function _OLD_path()
	{
		$path = \lib\url::dir();
		if(isset($path[0]) && $path[0] === \lib\url::content())
		{
			unset($path[0]);
		}
		if(is_array($path) && $path)
		{
			$path = implode('/', $path);
		}
		else
		{
			$path = null;
		}

		if($lang = \lib\url::lang())
		{
			$path = $lang . '/'. $path;
		}

		$new_url = $path;
		return $new_url;
	}

	private static function _breadcrumb()
	{
		$breadcrumb = \lib\url::dir();
		if(isset($breadcrumb[0]) && $breadcrumb[0] === \lib\url::content())
		{
			unset($breadcrumb[0]);
		}

		if(is_array($breadcrumb) && $breadcrumb)
		{
			$temp = [];
			foreach ($breadcrumb as $key => $value)
			{
				if(strpos($value, '=') !== false)
				{
					$split = explode('=', $value);
					array_push($temp, $split[0]);
				}
				else
				{
					array_push($temp, $value);
				}
			}
		}
		else
		{
			$breadcrumb = [];
		}

		$new_url = $breadcrumb;
		return $new_url;
	}


	private static function _module()
	{
		$module = \lib\url::dir();
		if(isset($module[0]) && $module[0] === \lib\url::content())
		{
			unset($module[0]);
		}

		$module = array_values($module);

		if(is_array($module) && isset($module[0]))
		{
			$module = $module[0];
		}
		else
		{
			$module = null;
		}

		return $module;
	}

	private static function _child()
	{
		$module = \lib\url::dir();
		if(isset($module[0]) && $module[0] === \lib\url::content())
		{
			unset($module[0]);
		}

		$module = array_values($module);

		if(is_array($module) && isset($module[0]))
		{
			$module = $module[0];
		}
		else
		{
			$module = null;
		}

		if(isset($module[0]) && $module[0] === $module)
		{
			unset($module[0]);
		}

		$module = array_values($module);

		if(is_array($module) && isset($module[0]))
		{
			$child = $module[0];
		}
		else
		{
			$child = null;
		}

		return $child;

	}

	private static function _tags()
	{

	}

	private static function _cats()
	{

	}

	private static function _pages()
	{

	}

	private static function _LoginService()
	{
		$new_url = null;
		$new_url .= \lib\url::protocol(). '://';
		$new_url .= \lib\url::domain(). '/account';
		return $new_url;
	}

	private static function _account()
	{
		$new_url = null;
		$new_url .= \lib\url::protocol(). '://';
		$new_url .= \lib\url::domain(). '/account';
		return $new_url;
	}

	private static function _MainService()
	{
		$new_url = \lib\url::protocol(). '://ermile.'. \lib\url::tld();
		return $new_url;
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
