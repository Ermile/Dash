<?php
namespace lib\engine;


class prepare
{
	public static function requirements()
	{
		self::hi_developers();
		self::minimum_requirement();

		self::error_handler();
		self::debug();
	}


	public static function basics()
	{
		// check comming soon page
		self::coming_soon();
		// check need redirect for lang or www or https or main domain
		self::appropriate_url();
		self::account_urls();

		// start session
		self::session_start();

		self::user_country_redirect();
	}



	/**
	* if the user use 'en' language of site
	* and her country is "IR"
	* and no referer to this page
	* and no cookie set from this site
	* redirect to 'fa' page
	* WARNING:
	* this function work when the default lanuage of site is 'en'
	* if the default language if 'fa'
	* and the user work by 'en' site
	* this function redirect to tj.com/fa/en
	* and then redirect to tj.com/en
	* so no change to user interface ;)
	*/
	private static function user_country_redirect()
	{
		if(\lib\url::isLocal())
		{
			return;
		}

		if(\lib\agent::isBot())
		{
			return ;
		}

		$referer = (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) ? true : false;

		if($referer)
		{
			return;
		}

		$key = 'language';

		$cookie = \lib\utility\cookie::read($key);

		if(!$_SESSION && !$cookie && !\lib\url::lang())
		{
			$default_site_language = \lib\language::default();
			$country_is_ir         = (isset($_SERVER['HTTP_CF_IPCOUNTRY']) && mb_strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']) === 'IR') ? true : false;
			$redirect_lang         = null;

			$access_lang = \lib\option::language('list');

			if($default_site_language === 'fa' && !$country_is_ir)
			{
				$redirect_lang = 'en';
			}
			elseif($default_site_language === 'en' && $country_is_ir)
			{
				$redirect_lang = 'fa';
			}
			$cookie_lang = $redirect_lang ? $redirect_lang : $default_site_language;
			$domain = '.'. \lib\url::domain();

			\lib\utility\cookie::write($key, $cookie_lang, (60*60*24*30), $domain);
			$_SESSION[$key] = $cookie_lang;

			if($redirect_lang && array_key_exists($redirect_lang, $access_lang))
			{
				$root    = \lib\url::base();
				$full    = \lib\url::pwd();
				$new_url = str_replace($root, $root. '/'. $redirect_lang, $full);
				\lib\redirect::to($new_url);
			}
		}
	}


	/**
	 * start session
	 */
	private static function session_start()
	{
		if(is_string(\lib\url::root()))
		{
			session_name(\lib\url::root());
		}

		// set session cookie params
		session_set_cookie_params(0, '/', '.'.\lib\url::domain(), false, true);

		// start sessions
		session_start();
	}


	/**
	 * [account_urls description]
	 * @return [type] [description]
	 */
	private static function account_urls()
	{
		$param = \lib\url::query();
		if($param)
		{
			$param = '?'.$param;
		}

		$myrep = \lib\url::content();
		switch (\lib\url::module())
		{
			case 'signin':
			case 'login':
				$url = \lib\url::base(). '/enter'. $param;
				\lib\redirect::to($url);
				break;

			case 'signup':
				if($myrep !== 'enter')
				{
					$url = \lib\url::base(). '/enter/signup'. $param;
					\lib\redirect::to($url);
				}
				break;

			case 'register':

				$url = \lib\url::base(). '/enter/signup'. $param;
				\lib\redirect::to($url);
				break;

			case 'signout':
			case 'logout':
				if($myrep !== 'enter')
				{
					$url = \lib\url::base(). '/enter/logout'. $param;
					\lib\redirect::to($url);
				}

				break;
		}

		switch (\lib\url::directory())
		{
			case 'account/recovery':
			case 'account/changepass':
			case 'account/verification':
			case 'account/verificationsms':
			case 'account/signin':
			case 'account/login':
				$url = \lib\url::base(). '/enter'. $param;
				\lib\redirect::to($url);
				break;

			case 'account/signup':
			case 'account/register':
				$url = \lib\url::base(). '/enter/signup'. $param;
				\lib\redirect::to($url);
				break;

			case 'account/logout':
			case 'account/signout':
				$url = \lib\url::base(). '/enter/logout'. $param;
				\lib\redirect::to($url);
				break;
		}
	}


	/**
	 * set best domain and url
	 * @return [type] [description]
	 */
	private static function appropriate_url()
	{
		if(\lib\option::url('fix') !== true)
		{
			return null;
		}

		// decalare target url
		$target_url = '';

		// fix protocol
		if(\lib\option::url('protocol'))
		{
			$target_url = \lib\option::url('protocol').'://';
		}
		else
		{
			$target_url = \lib\url::protocol().'://';
		}

		// fix root domain
		if(\lib\option::url('root'))
		{
			$target_url .= \lib\option::url('root');
		}
		elseif(\lib\url::root())
		{
			$target_url .= \lib\url::root();
		}

		// fix tld
		if(\lib\option::url('tld'))
		{
			$target_url .= '.'.\lib\option::url('tld');
		}
		elseif(\lib\url::tld())
		{
			$target_url .= '.'.\lib\url::tld();
		}

		// fix port, add 443 later @check
		if(\lib\option::url('port') && \lib\option::url('port') !== 80)
		{
			$target_url .= ':'.\lib\option::url('port');
		}
		elseif(\lib\url::port() && \lib\url::port() !== 80)
		{
			$target_url .= ':'.\lib\url::port();
		}

		// help new language detect in target site by set /fa
		if(\lib\option::url('tld') !== \lib\url::tld())
		{
			switch (\lib\url::tld())
			{
				case 'ir':
					$target_url .= $target_url. "/fa";
					break;

				default:
					break;
			}
		}

		// if we have new target url, and dont on force show mode, try to change it
		if($target_url !== \lib\url::site() && !\lib\request::get('force'))
		{
			$myBrowser = \lib\utility\browserDetection::browser_detection('browser_name');
			if($myBrowser === 'samsungbrowser')
			{
				// samsung is stupid!
			}
			else
			{
				\lib\redirect::to($target_url);
			}
		}
	}


	/**
	 * check coming soon status
	 * @return [type] [description]
	 */
	private static function coming_soon()
	{
		/**
		 * in coming soon period show public_html/pages/coming/ folder
		 * developer must set get parameter like site.com/dev=anyvalue
		 * for disable this attribute turn off it from config.php in project root
		 */
		if(\lib\option::config('coming'))
		{
			// if user set dev in get, show the site
			if(isset($_GET['dev']))
			{
				setcookie('preview','yes',time() + 30*24*60*60,'/','.'.\lib\url::domain());
			}
			elseif(\lib\url::dir(0) === 'hook')
			{
				// allow telegram to commiunate on coming soon
			}
			elseif(!isset($_COOKIE["preview"]))
			{
				\lib\redirect::to(\lib\url::site().'/static/page/coming/', true, 302);
			}
		}
	}


	/**
	 * set custom error handler
	 */
	private function error_handler()
	{
		//Setting for the PHP Error Handler
		set_error_handler( "\\lib\\engine\\error::handle_error" );

		//Setting for the PHP Exceptions Error Handler
		set_exception_handler( "\\lib\\engine\\error::handle_exception" );

		//Setting for the PHP Fatal Error
		register_shutdown_function( "\\lib\\engine\\error::handle_fatal" );
	}


	/**
	 * set debug status
	 * @param  [type] $_status [description]
	 */
	public function debug($_status = null)
	{
		if($_status === null)
		{
			$_status = \lib\option::config('debug');
		}

		if($_status)
		{
			ini_set('display_startup_errors', 'On');
			ini_set('error_reporting'       , 'E_ALL | E_STRICT');
			ini_set('track_errors'          , 'On');
			ini_set('display_errors'        , 1);
			error_reporting(E_ALL);
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 0);
		}
	}


	/**
	 * check current version of server technologies like php and mysql
	 * and if is less than min, show error message
	 * @return [type] [description]
	 */
	private static function minimum_requirement()
	{
		// check php version to upper than 7.0
		if(version_compare(phpversion(), '7.0', '<'))
		{
			\lib\code::die("<p>For using Dash you must update php version to 7.0 or higher!</p>");
		}
	}

	/**
	 * set some header and say hi to developers
	 */
	private function hi_developers()
	{
		// change header and remove php from it
		@header("X-Made-In: Ermile!");
		@header("X-Powered-By: Dash!");
	}
}
?>