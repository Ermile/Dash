<?php
namespace lib\app\posts;
use \lib\debug;

trait get
{

	public static function get_category_tag($_post_id, $_type)
	{
		$post_id = \lib\utility\shortURL::decode($_post_id);
		if(!$post_id)
		{
			return false;
		}

		$result = \lib\db\termusages::usage($post_id, $_type);

		$temp = [];
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$temp[] = self::ready($value);
			}
		}

		return $temp;
	}

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


	public static function get_post_list($_options = [])
	{
		$default_options =
		[
			'limit' => 10,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$get_last_posts = \lib\db\posts::get_last_posts($_options);
		$temp = [];
		if(is_array($get_last_posts))
		{
			foreach ($get_last_posts as $key => $value)
			{
				$temp[] = self::ready($value);
			}
		}
		return $temp;
	}
}
?>