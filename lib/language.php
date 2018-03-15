<?php
namespace lib;


class language
{
	public static $language;
	public static $language_default;

	public static $data =
	[
		'en' => ['name' => 'en', 'direction' => 'ltr', 'iso' => 'en_US', 'localname' => 'English', 'country' => ['United Kingdom', 'United States']],
		'fa' => ['name' => 'fa', 'direction' => 'rtl', 'iso' => 'fa_IR', 'localname' => 'Persian - فارسی', 'country' => ['Iran']],
		'ar' => ['name' => 'ar', 'direction' => 'rtl', 'iso' => 'ar_SU', 'localname' => 'Arabic - العربية', 'country' => ['Saudi Arabia']],
	];

	public static function default()
	{
		return \lib\option::language('default');
	}


	/**
	 * get lost of languages
	 */
	public static function list($_request = null, $_index = null)
	{
		$list = \lib\option::language('list');
		return $list;
	}


	/**
	 * check language exist and return true or false
	 * @param  [type] $_lang   [description]
	 * @param  string $_column [description]
	 * @return [type]          [description]
	 */
	public static function check($_lang, $_column = 'name')
	{
		$lang_list = array_column(self::$data, $_column);
		if(in_array($_lang, $lang_list))
		{
			return true;
		}
		return false;
	}


	/**
	 * get lang
	 *
	 * @param      <type>  $_key      The key
	 * @param      string  $_request  The request
	 */
	public static function get($_key, $_request = 'iso')
	{
		$result = null;
		// if pass more than 2 character, then only use 2 char
		if(strlen($_key)> 2)
		{
			$_key = substr($_key, 0, 2);
		}
		if(!empty(self::$data) && isset(self::$data[$_key]))
		{
			if($_request === 'all' || !$_request)
			{
				$result = self::$data[$_key];
			}
			else
			{
				$result = self::$data[$_key][$_request];
			}
		}
		return $result;
	}


	/**
	 * return list of languages in current project
	 * read form folders exist in includes/languages
	 * @return [type] [description]
	 */
	public static function languages($_dir = false)
	{
		// detect languages exist in current project
		$langList = glob(dir_includes.'languages/*', GLOB_ONLYDIR);
		$myList   = ['en' => 'English'];
		foreach ($langList as $myLang)
		{
			$myLang     = preg_replace("[\\\\]", "/", $myLang);
			$myLang     = substr( $myLang, (strrpos($myLang, "/" )+ 1));
			$myLang     = substr($myLang, 0, 2);
			$myLangName = $myLang;
			$myLangDir  = 'ltr';
			switch (substr($myLang, 0, 2))
			{
				case 'fa':
					$myLangName = 'Persian - فارسی';
					$myLangDir  = 'rtl';
					break;

				case 'ar':
					$myLangName = 'Arabic - العربية';
					$myLangDir  = 'rtl';
					break;

				case 'en':
					$myLangName = 'English';
					$myLangDir  = 'ltr';
					break;

				case 'de':
					$myLangName = 'Deutsch';
					break;


				case 'fr':
					$myLangName = 'French';
					break;
			}
			$myList[$myLang] = $myLangName;
		}

		if($_dir)
		{
			return $myLangDir;

		}
		return $myList;
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
				language::set_language($my_first_url);
				// add this language to base url
				router::$prefix_base .= router::get_url(0);
				// remove language from url and continue
				router::remove_url($my_first_url);
				if(\lib\utility\location\languages::check(\lib\url::dir(0)))
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
		// 	language::set_language(substr(self::$language_default, 0, 2));
		// }
	}
}
?>