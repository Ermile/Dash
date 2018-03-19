<?php
namespace lib\engine;


class init
{
	public static function debug()
	{
		// change header and remove php from it
		header("X-Made-In: Ermile!");
		header("X-Powered-By: Dash!");

		if (\lib\option::config('debug'))
		{
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
	}


	public static function coming_soon()
	{
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
	}


	/**
	 * check current version of server technologies like php and mysql
	 * and if is less than min, show error message
	 * @return [type] [description]
	 */
	public static function minimum_requirement()
	{
		// check php version to upper than 7.0
		if(version_compare(phpversion(), '7.0', '<'))
		{
			\lib\code::die("<p>For using Dash you must update php version to 7.0 or higher!</p>");
		}
	}


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


	public static function appropriate_url()
	{
		self::account_urls();

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

		// fix port
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
				header('Location: '. $target_url, true, 301);
			}
		}
	}
}
?>