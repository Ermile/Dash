<?php
namespace dash\utility;

/** Cookies management **/
class cookie
{
	// Default configuration

	// @var int			Life time of the cookie in seconds
	const DURATION	= 0;

	// @var string		Path in which the cookie will be available
	const PATH		= '/';

	// @var string		Domain that the cookie is available
	const DOMAIN	= null;

	// @var bool
	// If true, the cookie should only be transmitted over a secure HTTPS connection from the client
	const SECURE	= false;

	// @var bool		If true, the cookie will be made accessible only through the HTTP protocol
	const HTTPONLY	= true;

	/**
	 * Reads a cookie and returns its value
	 *
	 * @param string $_name	Name of the cookie
	 * @return mixed	Value of the cookie
	 */
	public static function read($_name)
	{
		if(is_array($_COOKIE) && array_key_exists($_name, $_COOKIE))
		{
			return $_COOKIE[$_name];
		}

		return null;
	}


	/**
	 * Creates or modify a cookie
	 *
	 * @param string $_name		Name of the cookie
	 * @param string $_value		Value of the cookie. Destroy the cookie if omitted or null
	 * @param int $_duration 	Life time of the cookie. Uses default value if omitted or null
	 * @param string $_domain	Domain that the cookie is available. Uses default value if omitted or null
	 * @param string $_path		Path in which the cookie will be available. Uses default value if omitted or null
	 * @param bool $_secure		If true, the cookie should only be transmitted over a secure HTTPS connection from the client. Uses default value if omitted or null
	 * @param bool $_httponly	If true, the cookie will be made accessible only through the HTTP protocol. Uses default value if omitted or null
	 */
	public static function write($_name, $_value = null, $_duration = null, $_domain = null, $_path = null, $_secure = null, $_httponly = null)
	{
		if(!isset($_value))
		{
			return self::delete($_name, $_path, $_domain);
		}

		if(!isset($_duration))	$_duration = self::DURATION;
		if(!isset($_path))		$_path     = self::PATH;
		if(!isset($_domain))	$_domain   = self::DOMAIN;
		if(!isset($_secure))	$_secure   = self::SECURE;
		if(!isset($_httponly))	$_httponly = self::HTTPONLY;

		// Expiration date from the life time in seconds
		if($_duration == 0)
		{
			$expire = 0;
		}
		else
		{
			$expire = time()+((int) $_duration);
		}

		// The value must be a string
		$_value = (string) $_value;

		// Writes the cookie
		setcookie($_name, $_value, $expire, $_path, $_domain, $_secure, $_httponly);
		$_COOKIE[$_name] = $_value;
	}


	/**
	 * Deletes a cookie
	 *
	 * @param string $_name	Name of the cookie
	 */
	public static function delete($_name, $_path = null, $_domain = null)
	{
		if(!$_path)
		{
			$_path = self::PATH;
		}

		setcookie($_name, null, time()-3600*30, $_path, $_domain);

		unset($_COOKIE[$_name]);
	}

}
?>