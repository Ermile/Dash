<?php
namespace dash;


class language
{
	public static $language = [];
	public static $language_default = null;

	/**
	 * all language dash supported it
	 *
	 * @var        array
	 */
	public static $data =
	[
		'en' => ['name' => 'en', 'direction' => 'ltr', 'iso' => 'en_US', 'localname' => 'English'],
		'fa' => ['name' => 'fa', 'direction' => 'rtl', 'iso' => 'fa_IR', 'localname' => 'فارسی'],
		'ar' => ['name' => 'ar', 'direction' => 'rtl', 'iso' => 'ar_IQ', 'localname' => 'العربية'],
	];


	public static function primary()
	{
		// for ir domain default lang is fa
		if(\dash\url::tld() === 'ir')
		{
			return 'fa';
		}

		// get default lang
		$default = \dash\option::language('default');
		// if default is not exist, set en as default
		if(!$default)
		{
			$default = 'en';
		}
		return $default;
	}


	/**
	 * get lost of languages
	 */
	public static function all($_for_html = false)
	{
		$list = \dash\option::language('list');

		$temp = [];

		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				if(array_key_exists($value, self::$data))
				{
					if($_for_html)
					{
						if(isset(self::$data[$value]['localname']))
						{
							$temp[$value] = self::$data[$value]['localname'];
						}
					}
					else
					{
						$temp[$value] = self::$data[$value];
					}
				}
			}
		}
		$list = $temp;

		return $list;
	}


	/**
	 * check language exist and return true or false
	 * @param  [type] $_lang   [description]
	 * @param  string $_column [description]
	 * @return [type]          [description]
	 */
	public static function check($_lang, $_all = false)
	{
		if($_all)
		{
			return array_key_exists($_lang, self::$data);
		}
		else
		{
			return array_key_exists($_lang, self::all());
		}
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

		$site_lang = self::all();

		if(!empty($site_lang) && isset($site_lang[$_key]))
		{
			if($_request === 'all' || !$_request)
			{
				$result = $site_lang[$_key];
			}
			else
			{
				$result = $site_lang[$_key][$_request];
			}
		}
		return $result;
	}

	public static function dir()
	{
		return self::current('direction');
	}


	/**
	 * get detail of language
	 * @param  string $_request [description]
	 * @return [type]           [description]
	 */
	public static function current($_request = 'name')
	{
		if(!self::$language)
		{
			self::detect_language();
		}

		$result = null;
		if($_request === 'all')
		{
			$result = self::$language;
		}
		elseif(isset(self::$language[$_request]))
		{
			$result = self::$language[$_request];
		}
		return $result;
	}


	/**
	 * set language of service
	 * @param [type] $_language [description]
	 */
	public static function set_language($_language)
	{
		self::$language_default = self::primary();

		// get all detail of this language
		self::$language = self::get($_language, 'all');
		if(!self::$language)
		{
			self::$language = self::get(self::$language_default, 'all');
		}

		// use php gettext function
		require_once(lib.'engine/i18n/translator.inc');
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
		$url_lang = \dash\url::lang();
		if(self::check($url_lang))
		{
			self::set_language($url_lang);
		}
		else
		{
			self::set_language(self::primary());
		}
	}


	public static function langList()
	{
		$result       = null;
		$html         = array_column(func_get_args(), 'html');
		$all          = array_column(func_get_args(), 'all');
		$onlyLink     = array_column(func_get_args(), 'onlyLink');
		$class        = array_column(func_get_args(), 'class');
		$reset        = array_column(func_get_args(), 'reset');
		$langList     = \dash\data::get('lang', 'list');
		$urlBase      = \dash\url::base();
		$urlContent   = \dash\url::content();
		$urlPath      = \dash\url::path();
		$urlDirectory = \dash\url::directory();
		$currentlang  = \dash\language::current();
		$defaultlang  = \dash\language::primary();
		$urlParam     = \dash\url::query();
		$urlLocation  = '';

		// create location of link
		if($urlDirectory)
		{
			$urlLocation = '/'. $urlDirectory;
		}
		if($urlParam)
		{
			$urlLocation .= '?'. $urlParam;
		}


		if(!$all)
		{
			unset($langList[$currentlang]);
		}

		if($html)
		{
			$lang_string    = '';
			$urlPathCurrent = '';
			foreach ($langList as $key => $value)
			{
				$href           = $urlBase;
				$activeClass    = '';
				$urlPathCurrent = '';
				if($key === $currentlang)
				{
					$activeClass = ' class="active"';
				}
				if($defaultlang === $key)
				{
					$key = "";
				}
				if($key)
				{
					$href           .= '/'.$key;
				}

				if($urlContent)
				{
					$href           .= '/'. $urlContent;
					$urlPathCurrent .= '/'. $urlContent;
				}
				if($urlLocation)
				{
					if(\dash\url::content() === 'support')
					{
						if(\dash\url::module() === 'ticket')
						{
							// support ticket is the same
							$href           .= $urlLocation;
							$urlPathCurrent .= $urlLocation;
						}
						else
						{
							// do nothing
							// in support links in not exact in each language
							// because of that we are not add content
							// to addr of another language
						}
					}
					elseif(\dash\url::content() === null && \dash\data::datarow())
					{
						// do nothing
						// because we are in main content and datarow is filled
						// in this condition we dont know to have exact news in another lang
						// because of that all links related to main content
					}
					else
					{
						// add if we are not in cms
						$href           .= $urlLocation;
						$urlPathCurrent .= $urlLocation;
					}
				}

				$lang_string .= '<a href="'. $href . '"'. $activeClass;
				if($key)
				{
					$lang_string .= ' hreflang="'. $key. '"';
				}
				$lang_string .= ' data-direct>';
				$lang_string .= $value;
				$lang_string .= "</a>";
			}

			if(!$onlyLink)
			{
				if(is_array($class) && isset($class[0]))
				{
					$class = $class[0];
				}
				if(!is_string($class) || !$class)
				{
					$class = '';
				}
				else
				{
					$class = ' '. $class;
				}

				$lang_string = '<nav class="langlist'. $class. '" data-xhr="langlist" data-url="'. $urlPathCurrent. '">'. $lang_string .'</nav>';
			}

			return $lang_string;
		}
		else
		{
			return $langList;
		}
	}

}
?>