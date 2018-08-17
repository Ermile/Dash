<?php
namespace dash\app\user;


trait get
{


	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function get($_args, $_options = [])
	{

		$result = \dash\db\users::get($_args);

		if(is_array($result))
		{
			return self::ready($result);
		}

		return $result;
	}
}
?>