<?php
namespace dash\app\user;

trait datalist
{
	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function list($_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}

		$meta            = [];
		$result          = \dash\db\users::search(null, $meta);
		$temp            = [];
		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}
}
?>