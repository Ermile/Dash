<?php
namespace lib;
class define
{
	// declare variables to set only one time each one of this variables


	public function abc()
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
		\lib\engine\baby::block();

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
			elseif(\lib\url::dir(0) === 'saloos_tg')
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
}
?>