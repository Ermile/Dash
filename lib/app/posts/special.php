<?php
namespace dash\app\posts;


class special
{

	public static function list()
	{
		$list             = [];
		// $list['slider']   = T_("Slider");
		// $list['mustread'] = T_("Must Read");

		if(is_callable(['\\lib\\app\\posts\\special', 'list']))
		{
			$project_list = \lib\app\posts\special::list();
			if(is_array($project_list))
			{
				$list = array_merge($list, $project_list);
			}
		}

		return $list;
	}

	public static function check($_special)
	{
		$list = self::list();
		if(array_key_exists($_special, $list))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>