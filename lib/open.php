<?php
namespace lib;


class open
{
	// generate array of function names
	private static $function_names =
	[
		'get'    => null,
		'post'   => null,
		'put'    => null,
		'patch'  => null,
		'delete' => null,
		'link'   => null,
	];


	/**
	 * check licence of open of method if exist
	 * @param  [type] $_type [description]
	 * @return [type]        [description]
	 */
	public static function license($_type = null)
	{
		if(!$_type)
		{
			$_type = mb_strtolower(\lib\request::is());
		}
		if(array_key_exists($_type, self::$function_names))
		{
			return self::$function_names[$_type];
		}

		return false;
	}


	/**
	 * call function of methods and set fn name
	 * @param  [type] $_method [description]
	 * @param  [type] $_fn     [description]
	 * @return [type]          [description]
	 */
	public static function __callStatic($_method, $_fn)
	{
		if(array_key_exists($_method, self::$function_names))
		{
			if(isset($_fn[0]))
			{
				$_fn = $_fn[0];
			}
			else
			{
				$_fn = null;
			}
			if(!$_fn)
			{
				$_fn = $_method;
			}
			self::$function_names[$_method] = $_fn;
			return self::$function_names;
		}

		return false;
	}
}
?>