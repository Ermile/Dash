<?php
namespace addons\content_enter\main\tools;
use \lib\utility\visitor;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait login
{
	/**
	 * find redirect url
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function find_redirect_url($_url = null)
	{
		$host = \lib\url::base();
		if($_url)
		{
			return $_url;
		}
		// get url language
		// if have referer redirect to referer
		if(utility::get('referer'))
		{
			$host = utility::get('referer');
		}
		elseif(isset($_SESSION['enter_referer']) && $_SESSION['enter_referer'])
		{
			$host = $_SESSION['enter_referer'];
			unset($_SESSION['enter_referer']);
		}
		elseif(self::get_enter_session('first_signup'))
		{
			$host .= \lib\language::get_current_language_string();
			// if first signup
			if(\lib\option::config('enter', 'singup_redirect'))
			{
				$host .= '/'. \lib\option::config('enter', 'singup_redirect');
			}
			else
			{
				$host .= \lib\option::config('redirect');
			}
		}
		else
		{

			$language = \lib\db\users::get_language(self::user_data('id'));
			if($language && \lib\language::check($language))
			{
				$host .= \lib\language::get_current_language_string($language);
			}
			else
			{
				$host .= \lib\language::get_current_language_string();
			}

			$host .='/'. \lib\option::config('redirect');
		}

		return $host;
	}


	/**
	 * login if have remember me
	 */
	public static function login_by_remember($_url = null)
	{
		$cookie = \lib\db\sessions::get_cookie();
		if($cookie)
		{
			$user_id = \lib\db\sessions::get_user_id();
			if($user_id)
			{
				\lib\db\users::set_login_session($user_id);

				if(isset($_SESSION['main_account']))
				{
					// if the admin user login by this user
					// not save the session
				}
				else
				{
					\lib\db\sessions::set($user_id);
				}
			}
		}
	}


	/**
	 * login
	 */
	public static function enter_set_login($_url = null, $_auto_redirect = false)
	{

		\lib\db\users::set_login_session(self::user_data('id'));

		if(self::user_data('id'))
		{
			if(isset($_SESSION['main_account']) && isset($_SESSION['main_mobile']))
			{
				if(isset($_SESSION['user']['mobile']) && $_SESSION['user']['mobile'] === $_SESSION['main_mobile'])
				{
					\lib\db\sessions::set(self::user_data('id'));
				}
				// if the admin user login by this user
				// not save the session
			}
			else
			{
				// set remeber and save session
				\lib\db\sessions::set(self::user_data('id'));
				// check user status
				// if the user status is awaiting
				// set the user status as enable
				if(self::user_data('status') === 'awaiting' && is_numeric(self::user_data('id')))
				{
					\lib\db\users::update(['status' => 'active'], self::user_data('id'));
				}
			}
		}

		$url = self::find_redirect_url($_url);

		if($_auto_redirect)
		{
			// clean session
			self::clean_session();
			// go to new address
			self::go_redirect($url, false, true);
		}
		else
		{
			self::set_enter_session('redirect_url', $url);
			return $url;
		}

	}


	/**
	 * Sets the logout.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function set_logout($_user_id, $_auto_redirect = true)
	{

		if($_user_id && is_numeric($_user_id))
		{
			if(isset($_SESSION['main_account']) && isset($_SESSION['main_mobile']) && isset($_SESSION['user']['mobile']))
			{
				if($_SESSION['user']['mobile'] === $_SESSION['main_mobile'])
				{
					\lib\db\sessions::logout($_user_id);
				}
				// if the admin user login by this user
				// not save the session
			}
			else
			{
				// set this session as logout
				\lib\db\sessions::logout($_user_id);
			}
		}

		/**
		 * destroy user id
		 */
		\lib\user::destroy();

		$_SESSION['user']    = [];
		$_SESSION['contact'] = [];
		$_SESSION            = [];

		// unset and destroy session then regenerate it
		session_unset();
		if(session_status() === PHP_SESSION_ACTIVE)
		{
			session_destroy();
		}

		if($_auto_redirect)
		{
			// go to base
			self::go_to('main');
		}

	}
}
?>