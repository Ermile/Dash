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

}
?>
