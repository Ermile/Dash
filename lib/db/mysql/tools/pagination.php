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
}
?>
