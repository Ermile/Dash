<?php
namespace lib;


class method
{
	// generate array of function names
	private static $function_names =
	[
		'get'    => null,
		'post'   => null,
		'put'    => null,
		'patch'  => null,
		'delete' => null,
	];

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



	public static function get($_fn = null)
	{
		if(!$_fn)
		{
			$_fn = 'run';
		}
		self::$function_names[__FUNCTION__] = $_fn;
	}


	public static function post($_fn = null)
	{
		if(!$_fn)
		{
			$_fn = __FUNCTION__;
		}
		self::$function_names[__FUNCTION__] = $_fn;
	}

	public static function put($_fn = null)
	{
		if(!$_fn)
		{
			$_fn = __FUNCTION__;
		}
		self::$function_names[__FUNCTION__] = $_fn;
	}

}
?>