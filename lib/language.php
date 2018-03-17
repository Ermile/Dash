<?php
namespace lib;


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
		'en' => ['name' => 'en', 'direction' => 'ltr', 'iso' => 'en_US', 'localname' => 'English', 'country' => ['United Kingdom', 'United States']],
		'fa' => ['name' => 'fa', 'direction' => 'rtl', 'iso' => 'fa_IR', 'localname' => 'Persian - فارسی', 'country' => ['Iran']],
		'ar' => ['name' => 'ar', 'direction' => 'rtl', 'iso' => 'ar_SU', 'localname' => 'Arabic - العربية', 'country' => ['Saudi Arabia']],
	];


	public static function default()
	{
		$default = \lib\option::language('default');
		if(!$default)
		{
			$default = 'en';
		}
		return $default;
	}


	/**
	 * get lost of languages
	 */
	public static function list($_for_html = false)
	{
		$list = \lib\option::language('list');

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
	public static function check($_lang)
	{
		return array_key_exists($_lang, self::list());
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

		$site_lang = self::list();

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
	public static function set_language($_language)
	{
		self::$language_default = self::default();

		// get all detail of this language
		self::$language = self::get($_language, 'all');
		if(!self::$language)
		{
			self::$language = self::get(self::$language_default, 'all');
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
		$my_first_url = \lib\url::lang();
		if(self::check($my_first_url))
		{
			self::set_language($my_first_url);
		}
		else
		{
			self::set_language(self::default());
		}
	}
}
?>