<?php
namespace dash\utility;


class theme
{

	public static function all()
	{
		$all            = [];
		$all['default'] = ['name' => T_("Default"), 'image' => null, 'icon' => 'theme'];
		$all['black']   = ['name' => T_("Black"), 'image' => null, 'icon' => 'theme'];
		$all['white']   = ['name' => T_("White"), 'image' => null, 'icon' => 'theme'];
		return $all;
	}


	public static function check($_key)
	{
		$all = self::all();
		if(isset($all[$_key]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public static function get($_key, $_detail = null)
	{
		$all = self::all();
		if(isset($all[$_key]))
		{
			if($_detail)
			{
				if(isset($all[$_key][$_detail]))
				{
					return $all[$_key][$_detail];
				}
				else
				{
					return null;
				}
			}
			else
			{
				return $all[$_key];
			}
		}
		else
		{
			return false;
		}
	}
}
?>