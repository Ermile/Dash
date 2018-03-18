<?php
namespace lib\engine;
class prepare
{
	// declare variables to set only one time each one of this variables


	public static function abc()
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
			set_error_handler( "\\lib\\engine\\error::handle_error" );

			//Setting for the PHP Exceptions Error Handler
			set_exception_handler( "\\lib\\engine\\error::handle_exception" );

			//Setting for the PHP Fatal Error
			register_shutdown_function( "\\lib\\engine\\error::handle_fatal" );
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 0);
		}

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

		// change header and remove php from it
		header("X-Made-In: Ermile!");
		header("X-Powered-By: Dash!");

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

		// need check
		// if find 2slash together block!
		if(strpos($_SERVER['REQUEST_URI'], '//') !== false)
		{
			// route url like this
			// http://dash.local/enter?referer=http://dash.local/cp
			if(strpos($_SERVER['REQUEST_URI'], '?') === false || strpos($_SERVER['REQUEST_URI'], '?') > strpos($_SERVER['REQUEST_URI'], '//'))
			{
				\lib\header::status(404, 'What are you doing!');
			}
		}

	}
}
?>