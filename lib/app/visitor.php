<?php
namespace dash\app;


class visitor
{
	private static function merge_args($_args)
	{
		$default_args =
		[
			'period' => 'hours24',
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		return array_merge($default_args, $_args);
	}


	public static function total_visit($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_visit = \dash\db\visitors::total_visit($_args);
		return intval($total_visit);
	}

	public static function total_visitor($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_visitor = \dash\db\visitors::total_visitor($_args);
		return intval($total_visitor);
	}


	public static function total_avgtime($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_avgtime = \dash\db\visitors::total_avgtime($_args);
		return intval($total_avgtime);
	}

	public static function total_maxtrafictime($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_maxtrafictime = \dash\db\visitors::total_maxtrafictime($_args);
		return $total_maxtrafictime;
	}
}
?>