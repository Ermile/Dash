<?php
namespace dash;

/**
 * Get index of array
 * use in display.php
 * @example \dash\get::index($value, 'index1', 'index2', 'index3');
 * @return  $value['index1']['index2']['index3'];
 */
class get
{

	public static function index($_array, $_key = null, $_subkey = null, $_third_key = null)
	{
		if(!is_array($_array))
		{
			return null;
		}

		if(!isset($_key))
		{
			return $_array;
		}
		else
		{
			if(!isset($_subkey))
			{
				if(array_key_exists($_key, $_array))
				{
					return $_array[$_key];
				}
				else
				{
					return null;
				}
			}
			else
			{
				if(!isset($_third_key))
				{
					if(isset($_array[$_key]) && is_array($_array[$_key]))
					{
						if(array_key_exists($_subkey, $_array[$_key]))
						{
							return $_array[$_key][$_subkey];
						}
						else
						{
							return null;
						}
					}
					else
					{
						return null;
					}
				}
				else
				{
					if(isset($_array[$_key][$_subkey]) && is_array($_array[$_key][$_subkey]))
					{
						if(array_key_exists($_third_key, $_array[$_key][$_subkey]))
						{
							return $_array[$_key][$_subkey][$_third_key];
						}
						else
						{
							return null;
						}
					}
					else
					{
						return null;
					}
				}
			}
		}
	}
}
?>