<?php
namespace lib;

/**
 * Class for application.
 */
class app
{
	private static $REQUEST_APP = [];


	/**
	 * Init request
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function variable($_args)
	{
		if(is_array($_args))
		{
			$args = \lib\utility\safe::safe($_args);

			self::$REQUEST_APP = $args;
		}
	}


	/**
	 * get request
	 */
	public static function request($_name = null)
	{
		if($_name)
		{
			if(array_key_exists($_name, self::$REQUEST_APP))
			{
				return self::$REQUEST_APP[$_name];
			}

			return null;
		}
		else
		{
			return self::$REQUEST_APP;
		}
	}


	/**
	 * check the request has exist or no
	 *
	 * @param      <type>  $_name  The name
	 */
	public static function isset_request($_name)
	{
		if(array_key_exists($_name, self::$REQUEST_APP))
		{
			return true;
		}
		return false;
	}



	/**
	 * save log
	 */
	public static function log()
	{
		\lib\db\logs::set(...func_get_args());
	}


	/**
	 * Logs a meta.
	 */
	public static function log_meta($_level = null, $_array = [])
	{
		$log_meta = null;

		switch ($_level)
		{
			// EASY LOG
			case 1:
				$log_meta =
				[
					'data' => null,
					'meta' =>
					[
						'input' => \lib\app::request(),
						'args'  => $_array,
					],
				];
				break;

			// MEDIOM LOG
			case 2:
				$log_meta =
				[
					'data' => null,
					'meta' =>
					[
						'input'   => \lib\app::request(),
						'session' => $_SESSION,
						'args'    => $_array,
					],
				];
				break;

			// HARD LOG
			case 3:
				$log_meta =
				[
					'data' => null,
					'meta' =>
					[
						'request' => $_REQUEST,
						'server'  => $_SERVER,
						'session' => $_SESSION,
						'input'   => \lib\app::request(),
						'args'    => $_array,
					],
				];
				break;

			// not log detail
			default:
				$log_meta = null;

				break;
		}
		return $log_meta;
	}


	/**
	 * return the url of static logo file
	 */
	public static function static_logo_url()
	{
		$url = Protocol . '://' . Domain. '.'. Tld. '/';
		$url .= 'static/siftal/images/default/logo.png';
		return $url;
	}


	/**
	 * return the url of static logo file
	 */
	public static function static_image_url()
	{
		$url = Protocol . '://' . Domain. '.'. Tld. '/';
		$url .= 'static/siftal/images/default/image.png';
		return $url;
	}

}
?>