<?php
namespace addons\content_enter\main\tools;
use \lib\utility;
use \lib\debug;

trait go_to
{
	/**
	 * redirect to url
	 *
	 * @param      <type>  $_url   The url
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function go_to($_url = null)
	{

		$host = \lib\url::base();
		$host .= \lib\language::get_current_language_string();

		switch ($_url)
		{
			// redirect to base
			case 'base':
				$host .= '/enter';
				self::go_redirect($host);
				break;

			case 'main':
				self::go_redirect($host);
				break;

			case 'okay':
				if($url = self::get_enter_session('redirect_url'))
				{
					self::go_redirect($url, false, true);
				}
				break;

			default:
				self::go_redirect($_url);
				break;
		}
	}


	/**
	 * set redirect url
	 *
	 * @param      <type>  $_url   The url
	 */
	public static function go_redirect($_url, $_return = false, $_direct = false)
	{
		if($_direct)
		{
			debug::msg('direct', true);
		}

		$redirect = new \lib\redirector($_url);

		if($_return)
		{
			return $redirect->redirect(true);
		}
		else
		{
			// debug::msg("redirect", $redirect->redirect(true));
			$redirect->redirect();
		}

	}
}
?>