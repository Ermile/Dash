<?php
namespace lib\utility;

class pagination
{
	/**
	 * save every thing in temp to get every where
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function detail($_key, $_value = null)
	{
		if(isset($_value))
		{
			\lib\temp::set('pagination_'. $_key, $_value);
		}
		else
		{
			return \lib\temp::get('pagination_'. $_key);
		}
	}


	/**
	 * Gets the query limit.
	 *
	 * @param      <type>   $_total_rows  The total rows
	 * @param      integer  $_page        The page
	 * @param      integer  $_limit       The limit
	 *
	 * @return     <type>   The query limit.
	 */
	public static function get_query_limit($_total_rows, $_page = 1, $_limit = 10)
	{
		$page        = $_page ? $_page : 1;
		$page        = intval($page) > 0 ? intval($page) : 1;
		$_total_rows = intval($_total_rows);
		$_limit      = intval($_limit);
		$_limit      = $_limit ? $_limit : 10;

		if($page > 0)
		{
			$start_limit = $_limit * ($page - 1);
		}
		else
		{
			$start_limit = 0;
		}

		$end_limit = $_limit;

		// save some detail
		self::detail('start_limit', $start_limit);
		self::detail('end_limit', $end_limit);
		self::detail('page', $page);
		self::detail('limit', $_limit);
		self::detail('total_rows', $_total_rows);

		return [$start_limit, $end_limit];
	}


	public static function page_number()
	{
		$current    = intval(self::detail('page'));
		$limit      = intval(self::detail('limit'));
		$total_rows = intval(self::detail('total_rows'));
		$limit      = $limit ? $limit : 1;
		$total_page = ceil($total_rows / $limit);

		$first = ($current - 1) >= 1  ? ($current - 1) : 1;

		$result            = [];
		$result[]          = ['link' => 'http:sdfsdf', 'text' => $first , 'title' => T_('First'), 'class' => 'first'];
		$result[]          = ['link' => 'http:sdfsdf', 'text' => 1, 'title' => 'prev', 'class' => 'prev'];
		$result[]          = '...';
		$result[]          = 17;
		$result[]          = 18;
		$result[]          = 19;
		$result['current'] = $current;
		$result[]          = 21;
		$result[]          = 22;
		$result[]          = 23;
		$result[]          = '...';
		$result['end']     = ceil($total_rows / $limit);
		$result['next']    = $current + 1;
		return $result;
	}
}
?>
