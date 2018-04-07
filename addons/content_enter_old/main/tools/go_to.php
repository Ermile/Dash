<?php
namespace content_enter\main\tools;


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

		$host = \dash\url::base();

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
			\dash\notif::direct();
		}

		\dash\redirect::to($_url);
	}
}
?>