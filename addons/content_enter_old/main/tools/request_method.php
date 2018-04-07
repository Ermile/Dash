<?php
namespace content_enter\main\tools;


trait request_method
{

	/**
	 * Gets the request method.
	 *
	 * @return     <type>  The request method.
	 */
	public static function get_request_method()
	{
		if(isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
		{
			return \dash\safe::safe(mb_strtolower($_SERVER['REQUEST_METHOD']));
		}
		return false;
	}
}