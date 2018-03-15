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
		$page           = \lib\utility::get('page');
		$url_get_length = \lib\utility::get('length');

		$page           = $page && ctype_digit($page) ? $page : 1;
		$page           = intval($page) > 0 ? intval($page) : 1;
		$_total_rows    = intval($_total_rows);

		if($url_get_length && ctype_digit($url_get_length) && intval($url_get_length) <= 1000)
		{
			$limit = intval($url_get_length);
		}
		else
		{
			$limit = intval($_limit);
		}

		$limit = $limit ? $limit : 10;

		if($page > 0)
		{
			$start_limit = $limit * ($page - 1);
		}
		else
		{
			$start_limit = 0;
		}

		$end_limit = $limit;

		$total_page = ceil($_total_rows / $limit);

		// save some detail
		self::detail('start_limit', $start_limit);
		self::detail('end_limit', $end_limit);
		self::detail('page', $page);
		self::detail('total_page', $total_page);
		self::detail('limit', $limit);
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
				// $title  = T_("First page");
				$class  = 'first';
				break;

			case 'spliter':
				$link   = false;
				$page   = null;
				$text   = '...';
				// $title  = null;
				$class  = 'spliter';
				break;

			case 'end':
				$link   = true;
				$page   = $_page_number;
				$text   = $_page_number;
				// $title  = T_("End page");
				$class  = 'end';
				break;

			case 'current':
				$link   = false;
				$page   = $_page_number;
				$text   = $_page_number;
				// $title  = T_("Current page");
				$class  = 'active';
				break;

			case 'next':
				$link   = true;
				$page   = $_page_number;
				$text   = '';
				// $title  = T_("Next page");
				$class  = 'next';
				break;

			case 'prev':
				$link   = true;
				$page   = $_page_number;
				$text   = '';
				// $title  = T_("Prev page");
				$class  = 'prev';
				break;

			default:
				$link   = true;
				$page   = $_page_number;
				$text   = $_page_number;
				// $title  = T_("Page"). ' '. \lib\utility\human::fitNumber($_page_number);
				$class  = null;
				break;
		}

		$result =
		[
			'page'   => $page,
			'link'	 => $link,
			'text'   => $text ? \lib\utility\human::fitNumber($text) : null,
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
		$total_page = intval(self::detail('total_page'));

		if(\lib\option::config('pagination_count_link') && ctype_digit(\lib\option::config('pagination_count_link')))
		{
			$count_link = intval(\lib\option::config('pagination_count_link'));
		}
		else
		{
			$count_link = 7;
		}

		$result = [];

		if($total_page <= 1)
		{
			// no pagination needed
		}
		elseif($total_page === 2)
		{
			if($current === 1)
			{
				$result[] = self::make('current', $current);
				$result[] = self::make(null, 2);
			}
			elseif ($current === 2)
			{
				$result[] = self::make(null, 1);
				$result[] = self::make('current', $current);
			}
		}
		else
		{
			$count_link_fill = 0;
			$sb              = [];
			$sa              = [];
			$i               = 0;
			$pages           = [];

			while ($count_link_fill < $count_link)
			{
				$i++;

				if($i > $count_link)
				{
					break;
				}

				if($current - $i + 1 > 0)
				{
					if($current - $i +1 !== $current)
					{
						array_push($pages, $current - $i + 1);
						array_push($sb, $current - $i + 1);
					}
					$count_link_fill++;
				}

				if($count_link_fill < $count_link)
				{
					if($current + $i <= $total_page)
					{
						array_push($pages, $current + $i);
						array_push($sa, $current + $i );
						$count_link_fill++;
					}
				}
			}

			asort($pages);

			$sb = array_reverse($sb);

			if($current !== 1)
			{
				$result[] = self::make('prev', $current -1);
			}

			if(current($pages) - 1 == 1)
			{
				if(in_array(1, $pages) || $current === 1)
				{
					// needless to make first page
				}
				else
				{
					$result[] = self::make(null, 1);
				}
			}
			elseif(current($pages) - 1 >= 2)
			{
				$result[] = self::make('first', 1);
				$result[] = self::make('spliter');
			}

			foreach ($sb as $key => $value)
			{
				$result[] = self::make(null, $value);
			}

			$result[] = self::make('current', $current);

			foreach ($sa as $key => $value)
			{
				$result[] = self::make(null, $value);
			}

			if(end($pages) + 1 <= $total_page)
			{
				if(in_array($total_page, $pages) || $current === $total_page)
				{
					// needless to make end page
				}
				else
				{
					if(end($pages) + 1 < $total_page)
					{
						$result[] = self::make('spliter');
					}

					$result[] = self::make('end', $total_page);
				}
			}

			if($current !== $total_page)
			{
				$result[] = self::make('next', $current + 1);
			}
		}

		$this_link = \lib\url::current();
		$get       = \lib\utility::get(null, 'raw');
		unset($get['page']);

		foreach ($result as $key => $value)
		{
			if(isset($value['link']) && $value['link'])
			{
				$temp_get             = $get;
				$temp_get['page']     = $value['page'];
				$temp_link            = $this_link . '?'. http_build_query($temp_get);
				$result[$key]['link'] = $temp_link;
			}
		}

		return $result;
	}
}
?>
