<?php
namespace dash\db\mysql\tools;

trait pagination
{
	/**
	 * get pagnation
	 *
	 * @param      <type>  $_total_rows   The query
	 * @param      <type>  $_length  The length
	 *
	 * @return     <type>  array [startlimit, endlimit]
	 */
	public static function pagnation($_total_rows, $_length)
	{
		return \dash\utility\pagination::init($_total_rows, $_length);
	}


	public static function pagination_query($_query, $_length = 10, $_array = false)
	{
		$total_rows = \dash\db::get($_query, 'count', true);
		$total_rows = intval($total_rows);
		$result     = self::pagnation($total_rows, $_length);

		if($result)
		{
			if($_array)
			{
				return $result;
			}
			else
			{
				return "LIMIT ". implode(',', $result);
			}
		}
		else
		{
			if($_array)
			{
				return $result;
			}
			else
			{
				return null;
			}
		}
	}
}
?>
