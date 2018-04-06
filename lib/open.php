<?php
namespace dash;


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
	 * check license of open of method if exist
	 * @param  [type] $_type [description]
	 * @return [type]        [description]
	 */
	public static function license($_type = null, $_force_replace = false)
	{
		if(!$_type)
		{
			$_type = mb_strtolower(\dash\request::is());
		}
		if(array_key_exists($_type, self::$function_names))
		{
			if(self::$function_names[$_type] === null && $_force_replace)
			{
				// if name is not set, use method name on force mode
				return $_type;
			}
			// else return value was set
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