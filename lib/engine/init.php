<?php
namespace lib\engine;


class init
{

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


	public static function appropriate_url()
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