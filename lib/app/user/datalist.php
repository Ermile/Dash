<?php
namespace lib\app\user;

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
		if(!\lib\user::id())
		{
			return false;
		}

		$meta            = [];
		$meta['creator'] = \lib\user::id();
		$result          = \lib\db\users::search(\lib\user::id(), $meta);
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