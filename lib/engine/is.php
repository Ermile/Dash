<?php
namespace lib\engine;

class is
{

	/**
	 * check is ajax
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH'])  && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}


	/**
	 * get accept
	 *
	 * @param      <type>  $name   The name
	 *
	 * @return     <type>  ( description_of_the_return_value )
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
	 * is json accept
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function json_accept()
	{
		$ret = self::accept("application/json");
		if($ret)
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