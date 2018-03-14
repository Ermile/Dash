<?php
namespace lib;

class request
{

	/**
	 * check request method
	 * POST
	 * GET
	 * ...
	 *
	 * @param      <type>   $_name  The name
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function is($_name = null)
	{
		$request_method = \lib\server::get('REQUEST_METHOD');

		if($_name)
		{
			if(mb_strtoupper($_name) === $request_method)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return $request_method;
		}
	}


	/**
	 * @return check request is ajax or not
	 */
	public static function ajax()
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			return true;
		}

		return false;
	}


	/**
	 * @param  name of accept value to check
	 * @return check accept this type or not in this request
	 */
	public static function accept($name)
	{
		if(isset($_SERVER['HTTP_ACCEPT']))
		{
			return (strpos($_SERVER['HTTP_ACCEPT'], $name) !== false);
		}

		return null;
	}


	/**
	 * @return check json acceptable or not
	 */
	public static function json_accept()
	{
		$result = self::accept("application/json");
		if($result)
		{
			return true;
		}
		elseif(isset($_SERVER['Content-Type']) && preg_match("/application\/json/i", $_SERVER['Content-Type']))
		{
			return true;
		}

		return false;
	}
}
?>
