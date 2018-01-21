<?php
namespace lib\app\posts;
use \lib\debug;

trait get
{


	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function get($_id, $_options = [])
	{
		$default_options =
		[
			'debug'          => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(!\lib\user::id())
		{
			return false;
		}

		$id = \lib\utility\shortURL::decode($_id);

		if(!$id)
		{
			\lib\debug::error(T_("Invalid posts id"));
			return false;
		}

		$detail = \lib\db\posts::get(['id' => $id, 'limit' => 1]);

		$temp = [];

		if(is_array($detail))
		{
			$temp = self::ready($detail);
		}

		return $temp;
	}
}
?>