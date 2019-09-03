<?php
namespace dash\db\posts;

trait search
{

	/**
	 * search in posts
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_string = null, $_options = [])
	{
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of poll
			$_options['get_last'] = true;
		}

		$default_options =
		[

			// enable|disable paignation,
			"pagenation"       => true,

			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 10
			// set the limit   = null and pagenation = false to get all record whitout limit
			"limit"            => 10,

			// for manual pagenation set the statrt_limit and end limit
			"start_limit"      => 0,

			// for manual pagenation set the statrt_limit and end limit
			"end_limit"        => 10,

			// the the last record inserted to post table
			"get_last"         => false,

			// default order byASC you can change to DESC
			"order"            => "ASC",
			"order_raw"        => null,

			// custom sort by field
			"sort"             => null,

			// default we check the language of user
			// and load only the post was this language or her lang is null
			"check_language"   => true,
			"term"             => null,

		];
		$_options = array_merge($default_options, $_options);

		// ------------------ pagenation
		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		$limit         = null;

		if($_options['limit'])
		{
			$limit = $_options['limit'];
		}
		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			$order = " ORDER BY posts.id DESC ";
		}
		else
		{
			if($_options['sort'])
			{
				if(!preg_match("/\./", $_options['sort']) || $_options['sort'] === 'commentcount')
				{
					$order = " ORDER BY $_options[sort] $_options[order] ";
				}
				else
				{
					$order = " ORDER BY `$_options[sort]` $_options[order] ";
				}
			}
			else
			{
				$order = " ORDER BY posts.id $_options[order] ";
			}
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		if(isset($_options['language']))
		{
			$_options['check_language'] = false;
		}

		if($_options['check_language'] === true)
		{
			$language = \dash\language::current();
			$where[] = " (posts.language IS NULL OR posts.language = '$language') ";
		}

		if($_options['term'])
		{
			$where[] =
			"
				posts.id IN
				(
					SELECT
						termusages.related_id
					FROM
						termusages
					INNER JOIN terms ON terms.id = termusages.term_id
					WHERE
						terms.slug = '$_options[term]' AND
						termusages.related_id = posts.id
				)
			";
		}

		if(isset($_options['order_raw']) && $_options['order_raw'])
		{
			$myOrder = "ORDER BY";
			$order   = " $myOrder $_options[order_raw] ";
		}

		// ------------------ remove system index
		// unset some value to not search in database as a field
		unset($_options['pagenation']);
		unset($_options['limit']);
		unset($_options['get_last']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['order']);
		unset($_options['all']);
		unset($_options['check_language']);
		unset($_options['sort']);
		unset($_options['term']);
		unset($_options['order_raw']);

		$makeWhere = \dash\db\config::make_where($_options);
		if($makeWhere)
		{
			$where[] = $makeWhere;
		}

		// foreach ($_options as $key => $value)
		// {
		// 	if(is_array($value))
		// 	{
		// 		// for similar "posts.`field` LIKE '%valud%'"
		// 		$where[] = " posts.`$key` $value[0] $value[1] ";
		// 	}
		// 	else
		// 	{
		// 		$where[] = " posts.`$key` = '$value' ";
		// 	}
		// }

		$where = join($where, " AND ");

		$search = null;
		if($_string !== null)
		{

			$search =
			"(
				posts.title 	LIKE '%$_string%' OR
				posts.content 	LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($pagenation)
		{
			$pagenation_query = "SELECT	COUNT(*) AS `count` FROM posts WHERE $where $search ";
			$pagenation_query = \dash\db::get($pagenation_query, 'count', true);
			list($limit_start, $limit) = \dash\db::pagnation($pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$query =
		"
			SELECT
				posts.*,
				(SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND comments.type = 'comment') AS `commentcount`
			FROM
				posts
			WHERE
			$where
			$search
			$order
			$limit
		";

		$result = \dash\db::get($query);
		$result = \dash\utility\filter::meta_decode($result);
		return $result;
	}
}
?>
