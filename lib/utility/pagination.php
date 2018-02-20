<?php
namespace lib\utility;

class pagination
{
	public static $detail = [];
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
			self::$detail[$_key] = $_value;
		}
		else
		{
			if(array_key_exists($_key, self::$detail))
			{
				return self::$detail[$_key];
			}
			else
			{
				return null;
			}
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
	public static function init($_total_rows, $_limit = 10)
	{
		$page        = \lib\utility::get('page');
		$page        = $page && ctype_digit($page) ? $page : 1;
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

		$total_page = ceil($_total_rows / $_limit);

		// save some detail
		self::detail('start_limit', $start_limit);
		self::detail('end_limit', $end_limit);
		self::detail('page', $page);
		self::detail('total_page', $total_page);
		self::detail('limit', $_limit);
		self::detail('total_rows', $_total_rows);
		return [$start_limit, $end_limit];
	}

	private static function make($_type = null, $_page_number = null)
	{

		$page   = null;
		$text   = null;
		$title  = null;
		$class  = null;
		$link   = null;

		switch ($_type)
		{
			case 'first':
				$link   = true;
				$page   = $_page_number;
				$text   = $_page_number;
				$title  = T_("First page");
				$class  = 'first';
				break;

			case 'spliter':
				$page   = null;
				$text   = '...';
				$title  = null;
				$class  = 'spliter';
				break;

			case 'end':
				$link   = true;
				$page   = $_page_number;
				$text   = $_page_number;
				$title  = T_("End page");
				$class  = 'end';
				break;


			case 'current':
				$page   = $_page_number;
				$text   = $_page_number;
				$title  = T_("Current page");
				$class  = 'active';
				break;

			case 'next':
				$link   = true;
				$page   = $_page_number;
				$text   = '';
				$title  = T_("Next page");
				$class  = 'next';
				break;

			case 'prev':
				$link   = true;
				$page   = $_page_number;
				$text   = '';
				$title  = T_("Prev page");
				$class  = 'prev';
				break;

			default:
				$link   = true;
				$page   = $_page_number;
				$text   = $_page_number;
				$title  = T_("Page :page", ['page' => \lib\utility\human::fitNumber($_page_number)]);
				$class  = null;
				break;
		}

		$result =
		[
			'page'   => $page,
			'link'	 => $link,
			'text'   => \lib\utility\human::fitNumber($text),
			'title'  => $title,
			'class'  => $class,
		];
		return $result;
	}


	public static function page_number()
	{
		$current    = intval(self::detail('page'));
		$limit      = intval(self::detail('limit'));
		$total_rows = intval(self::detail('total_rows'));
		$limit      = $limit ? $limit : 1;
		$first      = ($current - 1) >= 1  ? ($current - 1) : 1;
		$end_page   = intval(self::detail('total_page'));
		$total_page = $end_page;
		$count_link = 7;

		$result   = [];

		$have_spliter_1  = false;
		$have_spliter_2  = false;
		$have_first_page = false;
		$have_end_page   = true;
		$ceil_2          = ceil($count_link / 2);

		if($current - $ceil_2 - 1 >= 1)
		{
			$have_spliter_1 = true;
		}

		if($total_page - ($current + $ceil_2)  >= 1)
		{
			$have_spliter_2 = true;
		}

		if($current !== 1)
		{
			$result[] = self::make('prev', $current -1);
		}

		if($have_spliter_1)
		{
			$result[] = self::make('first', 1);
			$result[] = self::make('spliter');
		}

		$count_link_fill = 0;
		$sb = [];
		$sa = [];
		$i = 0;
		while ($count_link_fill < $count_link) 
		{
			$i++;
			// try to minus current page
			if($current - $i + 1 > 0)	
			{
				// can minus
				if($current - $i +1 !== $current)
				{	
					array_push($sb, $current - $i + 1);
				}
				$count_link_fill++;
			}

			if($current + $i < $total_page)
			{
				array_push($sa, $current + $i );
				$count_link_fill++;
			}
		}

		$sb = array_reverse($sb);
		array_shift($sa);
		// var_dump($sb, $sa);exit();
		foreach ($sb as $key => $value) 
		{
			$result[] = self::make(null, $value);
		}

		$result[] = self::make('current', $current);

		foreach ($sa as $key => $value) 
		{
			$result[] = self::make(null, $value);
		}

		if($have_spliter_2)
		{
			$result[] = self::make('spliter');
			$result[] = self::make('end', $total_page);
		}

		if($current !== $total_page)
		{
			$result[] = self::make('next', $current + 1);
		}

		$this_link = \lib\url::full2();
		$get = \lib\utility::get(null, 'raw');
		unset($get['page']);

		foreach ($result as $key => $value)
		{
			if(isset($value['link']))
			{
				$temp_get = $get;
				$temp_get['page'] = $value['page'];
				$temp_link = $this_link . '?'. http_build_query($temp_get);
				$result[$key]['link'] = $temp_link;
			}
		}

		// var_dump($result);exit();
		return $result;
	}
}
?>
