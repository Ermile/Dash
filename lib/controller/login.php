<?php
namespace lib\controller;

trait login
{
	/**
	 * Return login status without parameter
	 * If you pass the name as all return all of user session
	 * If you pass specefic user data field name return it
	 * @param  [type] $_name [description]
	 * @return [type]        [description]
	 */
	public function login($_name = null)
	{
		if(isset($_name))
		{
			if($_name === "all")
			{
				return \lib\user::detail();
			}
			else
			{
				return \lib\user::detail($_name);
			}
		}

		if(\lib\user::id())
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	* check is set remember of this user and login by this
	*
	*/
	public function check_remeber_login()
	{
		$url = \lib\utility\safe::safe($_SERVER['REQUEST_URI']);

		// check if have cookie set login by remember
		if(!$this->login())
		{
			\addons\content_enter\main\tools\login::login_by_remember();
		}
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
	public function user_country_redirect()
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
			$default_site_language = \lib\language::get_language('default');
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
				$this->redirector($new_url)->redirect();
			}
		}
	}
}
?>