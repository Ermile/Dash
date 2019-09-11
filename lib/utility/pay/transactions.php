<?php
namespace dash\utility\pay;


class transactions
{

	private static function transaction_table_name($_fn)
	{
		$master_class_name = '\\dash\\db\\transactions';

		if(!\dash\url::subdomain())
		{
			return $master_class_name;
		}

		if(defined('transaction_table_name'))
		{
			$class_name = '\\lib\\db\\'. transaction_table_name;

			if(is_callable([$class_name, $_fn]))
			{
				return $class_name;
			}
			else
			{
				return $master_class_name;
			}
		}
		return $master_class_name;
	}


	/**
	 * start transaction
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function start($_args)
	{
		$fn = self::transaction_table_name('set');
		return $fn::set($_args);
	}


	public static function update()
	{
		$fn = self::transaction_table_name('update');
		return $fn::update(...func_get_args());
	}


	public static function calc_budget()
	{
		$fn = self::transaction_table_name('calc_budget');
		return $fn::calc_budget(...func_get_args());
	}

	public static function load()
	{
		$fn = self::transaction_table_name('load');
		return $fn::load(...func_get_args());
	}

	public static function load_banktoken()
	{
		$fn = self::transaction_table_name('load_banktoken');
		return $fn::load_banktoken(...func_get_args());
	}

	public static function load_banktoken_transaction_id()
	{
		$fn = self::transaction_table_name('load_banktoken_transaction_id');
		return $fn::load_banktoken_transaction_id(...func_get_args());
	}

	// public static function final_verify($_transaction_id)
	// {
	// 	$project_fn = ["\\lib\\db\\transactions", "final_verify"];
	// 	$dash_fn    = ["\\dash\\db\\transactions", "final_verify"];

	// 	if(is_callable($project_fn))
	// 	{
	// 		$namespace = $project_fn[0];
	// 		$function  = $project_fn[1];
	// 		return $namespace::$function($_transaction_id);
	// 	}
	// 	else
	// 	{
	// 		$namespace = $dash_fn[0];
	// 		$function  = $dash_fn[1];
	// 		return $namespace::$function($_transaction_id);
	// 	}
	// }


}
?>