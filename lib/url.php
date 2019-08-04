<?php
namespace dash;
/**
 * this lib handle url of our PHP framework, Dash
 * v 5.3
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
 * 'site'       => 'http://jibres.com'					[protocol+domain]
 * 'sitelang'   => 'http://jibres.com/en'				[site+lang]
 * 'base'       => 'http://ermile.jibres.com'			[protocol+host]
 *
 * 'path'       => '/en/a/thirdparty/general/edit/test=yes?id=5&page=8'
 * 'lang'       => 'en'
 * 'content'    => 'a'
 * 'prefix'     => 'en/a'								[lang+content]
 * 'module'     => 'thirdparty'
 * 'child'      => 'general'
 * 'subchild'   => 'edit'
 * 'dir'        => [ 0 => 'thirdparty', 1 => 'general', 2 => 'edit', 3=> 'test=yes']
 * 'directory'  => 'thirdparty/general/edit/test=yes'
 *
 * 'kingdom'    => 'http://ermile.jibres.com/en'
 * 'here'       => 'http://ermile.jibres.com/en/a'
 * 'this' 		=> 'http://ermile.jibres.com/en/a/thirdparty'
 * 'that' 		=> 'http://ermile.jibres.com/en/a/thirdparty/general'
 * 'current'    => 'http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes'
 * 'pwd'        => 'http://ermile.jibres.com/en/a/thirdparty/general/edit/test=yes?id=5&page=8'
 */
class url
{
	// declare variables
	private static $url = [];


	/**
	 * initialize url and detect them
	 * @return [type] [description]
	 */
	public static function initialize()
	{
		self::$url = [];

		// get base values from server
		self::$url['protocol']  = self::_protocol();
		self::$url['host']      = self::_host();
		self::$url['port']      = self::_port();
		self::$url['uri']       = self::_uri();
		self::$url['query']     = self::_query();

		// analyse host
		$analysed_host          = self::analyse_host(self::$url['host']);
		self::$url              = array_merge(self::$url, $analysed_host);

		// generate with host and protocol
		self::$url['domain']    = self::_domain();
		self::$url['site']      = self::_site();
		self::$url['base']      = self::_base();
		self::$url['static']    = self::_static();
		self::$url['siftal']    = self::_siftal();

		// generate with path
		self::$url['path']      = self::_path();
		$analysed_path          = self::analyse_path(self::$url['path']);
		self::$url              = array_merge(self::$url, $analysed_path);

		// generate with host and path
		self::$url['sitelang']  = self::_sitelang();
		self::$url['kingdom']   = self::_kingdom();
		self::$url['here']      = self::_here();
		self::$url['this']      = self::_this();
		self::$url['that']      = self::_that();
		self::$url['current']   = self::_current();
		self::$url['pwd']       = self::_pwd();

		// return final result
		return self::$url;
	}



	/**
	 * pwd - query
	 * @return string of url
	 */
	private static function _current()
	{
		$current_url = self::$url['base'];
		if(self::$url['path'])
		{
			$current_url .= self::$url['path'];
		}
		$current_url = strtok($current_url, '?');

		return $current_url;
	}


	/**
	 * full url with all parts
	 * @return string of url
	 */
	private static function _pwd()
	{
		$full_url = self::$url['base'];
		if(self::$url['path'])
		{
			$full_url .= self::$url['path'];
		}

		return $full_url;
	}


	/**
	 * here + module
	 * @return string of result
	 */
	private static function _this()
	{
		$this_url = self::$url['here'];
		if(self::$url['module'])
		{
			$this_url .= '/' . self::$url['module'];
		}

		return $this_url;
	}


	/**
	 * here + module
	 * @return string of result
	 */
	private static function _that()
	{
		$that_url = self::$url['this'];
		if(self::$url['child'])
		{
			$that_url .= '/' . self::$url['child'];
		}

		return $that_url;
	}


	/**
	 * base + prefix
	 * @return string of result
	 */
	private static function _here()
	{
		$here_url = self::$url['base'];
		if(self::$url['prefix'])
		{
			$here_url .= '/' . self::$url['prefix'];
		}
		return $here_url;
	}


	/**
	 * get path and analyse it for extract all part if exist
	 * @param  string $_path
	 * @return array of result
	 */
	private static function analyse_path($_path)
	{
		$path_result =
		[
			'lang'      => null,
			'content'   => null,
			'prefix'    => null,
			'dir'       => [],
			'directory' => null,
			'module'    => null,
			'child'     => null,
			'subchild'  => null,
		];

		$my_path = trim(strtok($_path, '?'), '/');

		// if we are in root, return empty path result
		if($my_path === "")
		{
			return $path_result;
		}
		// seperate in array
		$my_dir = explode('/', $my_path);

		// try to detect lang
		$maybe_lang = reset($my_dir);
		// maybe first is language
		if(strlen($maybe_lang) === 2 && \dash\language::check($maybe_lang, true))
		{
			// set language
			$path_result['lang']   = $maybe_lang;
			$path_result['prefix'] = $maybe_lang;
			array_shift($my_dir);
		}

		// if we have another string in path
		if(count($my_dir) > 0)
		{
			// try to detect content
			$maybe_content = reset($my_dir);
			// maybe first is language
			if($maybe_content && \dash\engine\content::load($maybe_content))
			{
				// set language
				$path_result['content'] = $maybe_content;
				if($path_result['prefix'])
				{
					$path_result['prefix'] .= '/'. $maybe_content;
				}
				else
				{
					$path_result['prefix'] = $maybe_content;
				}
				array_shift($my_dir);
			}
		}

		// if we have dir and some other things
		if(count($my_dir) > 0)
		{
			// set remain as dir
			$path_result['dir'] = $my_dir;
			// set directory
			$path_result['directory'] = implode('/', $my_dir);
			// set module
			if(isset($my_dir[0]))
			{
				$path_result['module'] = $my_dir[0];
			}
			// set child
			if(isset($my_dir[1]))
			{
				$path_result['child'] = $my_dir[1];
			}
			// set subchild
			if(isset($my_dir[2]))
			{
				$path_result['subchild'] = $my_dir[2];
			}
		}

		return $path_result;
	}


	/**
	 * return filterd uri as path
	 * @return string of url path
	 */
	private static function _path()
	{
		$my_path = self::$url['uri'];
		if(self::related_url())
		{
			$my_path = str_replace(self::related_url(), '', $my_path);
		}

		return $my_path;
	}

	public static function set_subdomain($_subdomain = null)
	{
		if($_subdomain)
		{
			$url = null;
			$url .= self::protocol(). '://';
			$url .= $_subdomain. '.';
			$url .= self::domain();
			if(self::lang())
			{
				$url .= '/'. self::lang();
			}
			return $url;
		}
		else
		{
			return self::sitelang();
		}
	}


	/**
	 * if we are in different address, return in
	 * @return string of another addr
	 */
	public static function related_url()
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
	 * get site url with language without subdomain
	 * @return string of site address
	 */
	private static function _sitelang()
	{
		$myAddr = '';
		$isFree = \dash\option::url('freeSubdomain');
		if($isFree)
		{
			$myAddr = self::_base();
		}
		else
		{
			$myAddr = self::_site();
		}

		if(isset(self::$url['lang']))
		{
			$myAddr .= '/'. self::$url['lang'];
		}
		return $myAddr;
	}



	/**
	 * get url base to used in tag or links
	 * @return sting of base
	 */
	private static function _base()
	{
		$my_base = self::$url['protocol'] . '://'. self::$url['host'];

		if(self::related_url())
		{
			$my_base .= self::related_url();
		}
		return $my_base;
	}


	/**
	 * get url base of static folder
	 * @return sting of static folder
	 */
	private static function _static()
	{
		$staticAddr = '/static';
		$isFree = \dash\option::url('freeSubdomain');
		if($isFree)
		{
			$staticAddr = self::_base(). $staticAddr;
		}
		else
		{
			$staticAddr = self::_site(). $staticAddr;
		}
		return $staticAddr;
	}


	/**
	 * get url base of static folder
	 * @return sting of static folder
	 */
	private static function _siftal()
	{
		$siftalAddr = '';
		$useDevMode = \dash\option::config('dev', 'siftal');

		if($useDevMode)
		{
			if(self::isLocal())
			{
				if(self::$url['protocol'] === 'https')
				{
					$siftalAddr = 'https://siftal.local/dist';
				}
				else
				{
					$siftalAddr = 'http://siftal.local/dist';
				}
			}
			else
			{
				$siftalAddr = 'https://siftal.ir/dist';
			}
		}
		else
		{
			$siftalAddr = self::_static(). '/siftal';
		}
		return $siftalAddr;
	}


	/**
	 * get url base to used in tag or links
	 * @return sting of base
	 */
	private static function _kingdom()
	{
		$my_kingdom = self::$url['base'];

		if(self::$url['lang'])
		{
			$my_kingdom .= '/'. self::$url['lang'];
		}
		return $my_kingdom;
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

		if(self::related_url())
		{
			$domain .= self::related_url();
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
				if(isset($_args) && isset($_args[0]) && $_args[0] === 'hash')
				{
					return md5($result);
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
