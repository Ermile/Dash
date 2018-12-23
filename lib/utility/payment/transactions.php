<?php
namespace dash\utility\payment;


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
		$_args['condition'] = 'request';
		$fn = self::transaction_table_name('start');
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


	public static function final_verify($_transaction_id)
	{
		$fn = self::transaction_table_name('final_verify');
		return $fn::final_verify($_transaction_id);
	}
}
?>