<?php
namespace lib;
class define
{
	// declare variables to set only one time each one of this variables
	private static $language;
	private static $language_default;

	public function __construct()
	{
		/**
		 * If DEBUG is TRUE you can see the full error description, If set to FALSE show userfriendly messages
		 * change it from project config.php
		 */
		if (!defined('DEBUG'))
		{
			if(\lib\option::config('debug'))
			{
				define('DEBUG', true);
			}
			else
			{
				define('DEBUG', false);
			}
		}

		if (DEBUG)
		{
			ini_set('display_errors'        , 'On');
			ini_set('display_startup_errors', 'On');
			ini_set('error_reporting'       , 'E_ALL | E_STRICT');
			ini_set('track_errors'          , 'On');
			ini_set('display_errors'        , 1);
			error_reporting(E_ALL);

			//Setting for the PHP Error Handler
			// set_error_handler('\lib\error::myErrorHandler');

			//Setting for the PHP Exceptions Error Handler
			// set_exception_handler('\lib\error::myErrorHandler');

			//Setting for the PHP Fatal Error
			// register_shutdown_function('\lib\error::myErrorHandler');
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 0);

		}

		// block baby to not allow to harm yourself :/
		\lib\baby::block();

		$cookie_domain = null;
		if(isset($_SERVER['HTTP_HOST']))
		{
			$urlHostSegments = explode('.', $_SERVER['HTTP_HOST']);
			// if have subdomain
		    if(count($urlHostSegments) > 2)
		    {
				$cookie_domain = $urlHostSegments[0];
		    }
		}

		// if($cookie_domain)
		// {
		// 	session_name($cookie_domain);
		// 	$cookie_domain = $cookie_domain. '.'. \lib\url::domain();
		// 	session_set_cookie_params(0, '/', $cookie_domain, false, true);
		// }
		// else
		// {
		// 	session_name(\lib\url::root());
		// 	$cookie_domain = \lib\url::domain();
		// 	session_set_cookie_params(0, '/');
		// }

		if(is_string(\lib\url::root()))
		{
			session_name(\lib\url::root());
		}
		// set session cookie params
		session_set_cookie_params(0, '/', '.'.\lib\url::domain(), false, true);
		/**
		 * A session is a way to store information (in variables) to be used across multiple pages.
		 * Unlike a cookie, the information is not stored on the users computer.
		 * access to session with this code: $_SESSION["test"]
		 */

		// if(is_string(\lib\url::root()))
		// {
		// 	session_name(\lib\url::root());
		// }

		// if(is_string($cookie_domain))
		// {
		// 	session_name($cookie_domain);
		// }

		// session_set_cookie_params(0, '/', $cookie_domain, false, true);

		// set session cookie params
		// if user enable saving sessions in db
		// temporary disable because not work properly
		if(false)
		{
			$handler = new \lib\utility\sessionHandler();
			session_set_save_handler($handler, true);
		}
		// start sessions
		session_start();

		/**
		 * in coming soon period show public_html/pages/coming/ folder
		 * developer must set get parameter like site.com/dev=anyvalue
		 * for disable this attribute turn off it from config.php in project root
		 */
		if(\lib\option::config('coming') || defined('CommingSoon'))
		{
			// if user set dev in get, show the site
			if(isset($_GET['local']))
			{
				setcookie('preview','yes',time() + 30*24*60*60,'/','.'.\lib\url::domain());
			}
			elseif(router::get_url(0) === 'saloos_tg')
			{
				// allow telegram to commiunate on coming soon
			}
			elseif(!isset($_COOKIE["preview"]))
			{
				header('Location: '.\lib\url::site().'/static/page/coming/', true, 302);
				\lib\code::exit();
			}
		}
		// change header and remove php from it
		header("X-Made-In: Ermile!");
		header("X-Powered-By: Dash!");

		\lib\language::detect_language();
		\lib\language::set_language(\lib\language::$language);
	}


	/**
	 * get detail of language
	 * @param  string $_request [description]
	 * @return [type]           [description]
	 */
	public static function get_language($_request = 'name')
	{
		$result = null;
		if($_request === 'all')
		{
			$result = self::$language;
		}
		elseif($_request === 'default')
		{
			$result = self::$language_default;
		}
		elseif(isset(self::$language[$_request]))
		{
			$result = self::$language[$_request];
		}
		return $result;
	}


	/**
	 * [check_language description]
	 * @param  [type] $_language [description]
	 * @return [type]            [description]
	 */
	public static function get_current_language_string($_language = null, $_boolean = false)
	{
		$result = null;
		if(!$_language)
		{
			$_language = self::$language;
			$_language = $_language['name'];
		}
		$default_lang = substr(self::$language_default, 0, 2);
		if($default_lang !== $_language)
		{
			$result = '/'. $_language;
		}

		if($_boolean)
		{
			if($result !== null)
			{
				$result = true;
			}
			else
			{
				$result = false;
			}
		}
		return $result;
	}


	/**
	 * set language of service
	 * @param [type] $_language [description]
	 */
	public static function set_language($_language, $_force = false)
	{

		// if language is set and force is not set then return null
		if(self::$language && !$_force)
		{
			return null;
		}
		// if default language is not set, then set it only one time
		if(!self::$language_default)
		{
			self::$language_default = \lib\language::default();
			if(!self::$language_default)
			{
				self::$language_default = 'en';
			}
		}
		// get all detail of this language
		self::$language = \lib\utility\location\languages::get($_language, 'all');
		if(!self::$language)
		{
			self::$language = \lib\utility\location\languages::get(self::$language_default, 'all');
		}

		// use php gettext function
		require_once(lib.'utility/gettext/gettext.inc');
		// if we have iso then trans
		if(isset(self::$language['iso']))
		{
			// gettext setup
			T_setlocale(LC_MESSAGES, (self::$language['iso']));
			// Set the text domain as 'messages'
			T_bindtextdomain('messages', root.'includes/languages');
			T_bind_textdomain_codeset('messages', 'UTF-8');
			T_textdomain('messages');
		}
	}


	public static function detect_language()
	{
		// if default language is not set, then set it only one time
		if(!self::$language_default)
		{
			self::$language_default = \lib\language::default();
			if(!self::$language_default)
			{
				self::$language_default = 'en';
			}
		}

		// Step1
		// if language exist in url like ermile.com/fa/ then simulate remove it from url
		$my_first_url = router::get_url(0);
		if(\lib\utility\location\languages::check($my_first_url))
		{
			if(substr(self::$language_default, 0, 2) === $my_first_url)
			{
				$redirectURL = router::get_url();
				if(substr($redirectURL, 0, 2) === $my_first_url)
				{
					$redirectURL = substr($redirectURL, 2);
				}
				if(!$redirectURL)
				{
					$redirectURL = '/';
				}

				if(router::get_url(0) === 'api' || router::get_url(1) === 'api')
				{
					router::remove_url($my_first_url);
					// not redirect in api mode
				}
				else
				{
					$myredirect = new \lib\redirector($redirectURL);
					$myredirect->redirect();
				}
			}
			else
			{
				// set language
				define::set_language($my_first_url);
				// add this language to base url
				router::$prefix_base .= router::get_url(0);
				// remove language from url and continue
				router::remove_url($my_first_url);
				if(\lib\utility\location\languages::check(\lib\router::get_url(0)))
				{
					\lib\error::page("More than one language found");
				}
			}

		}

		// Step2 re
		// if we are not in dev and tld lang is exist
		// then use only one domain for this site then redirect to main tld

		// $tld_lang = \lib\utility\location\tld::get();
		// if(defined('MainService') && \lib\url::isLocal() === false)
		// {
		// 	/**
		// 	 need fix
		// 	 */
		// 	// for example redirect ermile.ir to ermile.com/fa
		// 	$myredirect = new \lib\redirector();
		// 	$myredirect->set_domain()->set_url($tld_lang)->redirect();
		// 	return false;
		// }

		// if language is not set
		// if(!self::$language)
		// {
		// 	define::set_language(substr(self::$language_default, 0, 2));
		// }
	}
}
?>