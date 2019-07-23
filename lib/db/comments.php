<?php
namespace dash\db;

/** comments managing **/
class comments
{
	/**
	 * this library work with comments
	 * v1.0
	 */


	/**
	 * insert new recrod in comments table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		\dash\db\config::public_insert('comments', ...func_get_args());
		return \dash\db::insert_id();
	}


	public static function get_count()
	{
		return \dash\db\config::public_get_count('comments', ...func_get_args());
	}


	public static function close_solved_ticket()
	{
		$yesterday = date("Y-m-d H:i:s", strtotime('-1 days'));
		$get_count =
		"
			SELECT COUNT(*) AS `count` FROM comments
			WHERE
				comments.solved = 1 AND
				comments.type   = 'ticket' AND
				comments.parent IS NULL AND
				comments.status IN ('answered', 'awaiting') AND
				comments.datemodified < '$yesterday'
		";

		$count = \dash\db::get($get_count, 'count', true);
		if($count)
		{
			\dash\log::set('ticket_AutoCloseSolvedTicket', ['count' => $count]);
		}

		$query =
		"
			UPDATE comments
			SET comments.status = 'close'
			WHERE
				comments.solved = 1 AND
				comments.type   = 'ticket' AND
				comments.parent IS NULL AND
				comments.status IN ('answered', 'awaiting') AND
				comments.datemodified < '$yesterday'
		";
		\dash\db::query($query);

	}

	public static function spam_by_block_ip()
	{
		$block_ip = \dash\utility\ip::list('block', true);
		if(is_array($block_ip) && $block_ip)
		{
			$ips = implode(',', $block_ip);

			$get_count =
			"
				SELECT COUNT(*) AS `count` FROM comments
				WHERE
					comments.status NOT IN ('spam') AND
					comments.ip IN ($ips)
			";

			$count = \dash\db::get($get_count, 'count', true);

			if($count)
			{
				\dash\log::set('ticket_AutoSpamTicketByIp', ['count' => $count]);
			}

			$query =
			"
				UPDATE comments
				SET comments.status = 'spam'
				WHERE
					comments.status NOT IN ('spam') AND
					comments.ip IN ($ips)
			";
			\dash\db::query($query);
		}

	}

	public static function get_user_in_ticket($_ids)
	{
		$query =
		"
			SELECT
				users.id,
				users.displayname,
				users.avatar
			FROM
				users
			WHERE
				users.id IN
				(
					SELECT DISTINCT comments.user_id FROM comments WHERE comments.parent IN ($_ids)
				)
		";

		return \dash\db::get($query);
	}



	public static function get_comment_counter($_args)
	{
		$where       = \dash\db\config::make_where($_args);
		$query_where = null;

		if($where)
		{
			$query_where = "WHERE ". $where;
		}

		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				comments.status
			FROM
				comments
				$query_where
			GROUP BY comments.status
		";

		$result = \dash\db::get($query, ['status', 'count']);
		return $result;

	}


	public static function search_full($_string = null, $_options = null)
	{
		$limit = null;
		if(isset($_options['limit']))
		{
			$limit = intval($_options['limit']);
		}

		unset($_options['limit']);

		$q = null;

		if($_options)
		{
			$q = \dash\db\config::make_where($_options);
		}

		if(isset($_string))
		{
			$_string = \dash\db\safe::value($_string);
			$q       .= "AND comments.content LIKE '%$_string%' ";
		}

		$pagination_query =
		"
			SELECT
				COUNT(*) AS `count`
			FROM
				comments
			WHERE
				$q
		";

		$limit = \dash\db::pagination_query($pagination_query, $limit);

		$query =
		"
			SELECT

				comments.*,
				users.avatar,
				users.displayname,
				users.gender,
				posts.title AS `post_title`
			FROM
				comments
			LEFT JOIN users ON users.id = comments.user_id
			LEFT JOIN posts ON posts.id = comments.post_id
			WHERE
				$q
			ORDER BY
				comments.datecreated DESC
			$limit
		";
		$result = \dash\db::get($query);

		return $result;
	}


	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_string = null, $_options = [])
	{

		$default_options =
		[
			"search_field"        =>
			"
				(
					comments.content LIKE '%__string__%'
				)
			",
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(isset($_options['join_user']) && $_options['join_user'])
		{
			if((isset($_options['get_tag']) && $_options['get_tag']) || (isset($_options['search_tag']) && $_options['search_tag']))
			{
				$_options['public_show_field'] =
				"
					comments.*,
					users.avatar,
					users.firstname,
					users.displayname,
					(
						SELECT GROUP_CONCAT(DISTINCT uComment.user_id)
						FROM comments AS `uComment`
						WHERE uComment.parent = comments.id
						ORDER BY uComment.id ASC

					) AS `user_in_ticket`,
					(
						SELECT GROUP_CONCAT(terms.title)
						FROM terms
						INNER JOIN termusages ON termusages.term_id = terms.id
						WHERE termusages.related = 'comments'
						AND termusages.related_id = comments.id
					) AS `tag`
				 ";
			}
			else
			{
				$_options['public_show_field'] =
				"
					comments.*,
					(
						SELECT GROUP_CONCAT(DISTINCT uComment.user_id)
						FROM comments AS `uComment`
						WHERE uComment.parent = comments.id
						ORDER BY uComment.id ASC

					) AS `user_in_ticket`,
					users.avatar,
					users.firstname,
					users.displayname
				";
			}

			$_options['master_join'] = "LEFT JOIN users ON users.id = comments.user_id ";
			$_options["search_field"] =
			"	(
						comments.content LIKE '%__string__%' OR
						comments.title LIKE '%__string__%' OR
						users.mobile LIKE '%__string__%' OR
						comments.id = '__string__'
				)
			";

			if(isset($_options['search_tag']) && $_options['search_tag'])
			{
				$_options['master_join'] =
				"
					LEFT JOIN users ON users.id = comments.user_id
					INNER JOIN termusages ON termusages.related_id = comments.id AND termusages.related = 'comments'
					INNER JOIN terms ON terms.id  = termusages.term_id
				";

				$_options['terms.slug'] = $_options['search_tag'];
			}

			unset($_options['search_tag']);
		}

		$result = \dash\db\config::public_search('comments', $_string, $_options);

		return $result;
	}

	public static function ticket_avg_first($_where)
	{
		$where = \dash\db\config::make_where($_where);
		if($where)
		{
			$time = date("Y-m-d H:i:s", strtotime("-30 days"));
			$query = " SELECT AVG(comments.answertime) AS `average` FROM comments WHERE $where AND comments.answertime IS NOT NULL AND comments.datecreated > '$time' ";
			$result = \dash\db::get($query, 'average', true);
			return intval($result) / 60 ;
		}

		return 0;
	}

	public static function ticket_avg_archive($_where)
	{
		$where = \dash\db\config::make_where($_where);
		if($where)
		{
			$time = date("Y-m-d H:i:s", strtotime("-30 days"));
			$query = " SELECT AVG(TIMESTAMPDIFF(SECOND,comments.datecreated, comments.datemodified)) AS `average` FROM comments WHERE $where AND comments.datecreated > '$time'  ";
			$result = \dash\db::get($query, 'average', true);
			return intval($result) / 60 / 60;
		}
		return 10;
	}


	public static function ticket_tag($_where)
	{
		$where = \dash\db\config::make_where($_where);
		if($where)
		{
			$query =
			"
				SELECT
					terms.id,
					terms.slug,
					terms.title,
					terms.meta,
					terms.status,
					count(*) AS `useage_count`
				FROM
					terms
				INNER JOIN termusages ON termusages.term_id  = terms.id
				INNER JOIN comments ON comments.id  = termusages.related_id
				WHERE
					termusages.type = 'support_tag' AND
					$where
				GROUP BY
					terms.id,
					terms.slug,
					terms.title,
					terms.meta,
					terms.status
			";
			$result = \dash\db::get($query);

			return $result;
		}
	}

	public static function ticket_mine($_user_id)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		$query = " SELECT COUNT(DISTINCT comments.parent) AS `count` FROM comments WHERE comments.type = 'ticket' AND comments.user_id = $_user_id ";
		$result = \dash\db::get($query, 'count', true);
		return intval($result);
	}


	/**
	 * get the comment
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('comments', ...func_get_args());
	}

	/**
	 * update field from comments table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('comments', ...func_get_args());
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		// get id
		$query =
		"
			UPDATE comments
			SET comments.status = 'deleted'
			WHERE comments.id = $_id
			LIMIT 1
		";

		return \dash\db::query($query);
	}


	public static function hard_delete($_id)
	{
		$query = "DELETE FROM comments WHERE comments.id = $_id LIMIT 1";
		return \dash\db::query($query);
	}


	/**
	 * save a comments
	 *
	 * @param      <type>  $_content  The content
	 * @param      array   $_args     The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function save($_content, $_args = null)
	{
		$values =
		[
			"post_id"    => null,
			"author"     => null,
			"email"      => null,
			"url"        => null,
			// "content" => null,
			"meta"       => null,
			"status"     => null,
			"parent"     => null,
			"user_id"    => null,
			"visitor_id" => null,
		];

		if(!$_args)
		{
			$_args = [];
		}
		// foreach args if isset use it
		foreach ($_args as $key => $value)
		{
			$value = "'". $value. "'";
			// check in normal condition exist
			if(array_key_exists($key, $values))
			{
				$values[$key] = $value;
			}
			// check for id
			$newKey = $key.'_id';
			if(array_key_exists($newKey, $values))
			{
				$values[$newKey] = $value;
			}
			// check for table prefix
			$newKey = ''. $key;
			if(array_key_exists($newKey, $values))
			{
				$values[$newKey] = $value;
			}
		}
		foreach ($values as $key => $value)
		{
			if(!$value)
			{
				unset($values[$key]);
			}
		}

		// set not null fields
		// set comment content
		$values['content'] = "'". htmlspecialchars($_content). "'";
		// set comment status if not set
		if(!isset($values['status']))
		{
			$values['status'] = "'unapproved'";
		}
		// set time of comment
		if(isset($values['meta']) && is_array($values['meta']))
		{
			$values['meta']['time'] = date('Y-m-d H:i:s');
		}
		else
		{
			$values['meta'] = ['time' => date('Y-m-d H:i:s')];
		}
		$values['meta'] = "'".json_encode($values['meta'], JSON_UNESCAPED_UNICODE)."'";
		// generate query text
		$list_field  = array_keys($values);
		$list_field  = implode($list_field, ', ');
		$list_values = implode($values, ', ');
		// create query string
		$qry       = "INSERT INTO comments ( $list_field ) VALUES ( $list_values )";

		// run query and insert into db
		$result    = \dash\db::query($qry);
		// get insert id
		$commentId = \dash\db::insert_id();
		// return last insert id
		return $commentId;
	}


	/**
	 * Gets the post comment.
	 *
	 * @param      <type>   $_post_id  The post identifier
	 * @param      integer  $_limit    The limit
	 * @param      boolean  $_user_id  The user identifier
	 *
	 * @return     <type>   The post comment.
	 */
	public static function get_comment($_post_id, $_limit = 6, $_user_id = false)
	{
		if(!is_numeric($_limit))
		{
			$_limit = 6;
		}

		if($_user_id)
		{
			$_limit = $_limit - 1;
		}

		$query =
		"
		(
			SELECT
				comments.id AS `id`,
				comments.content,
				comments.datecreated,
				comments.star,
				users.displayname,
				users.avatar
			FROM
				comments
			LEFT JOIN users ON users.id = comments.user_id
			WHERE
				comments.post_id        = $_post_id AND
				comments.status = 'approved' AND
				comments.type   = 'comment'
			ORDER BY RAND()
			LIMIT $_limit
		)
		";
		if($_user_id)
		{
			$query .=
			"
			UNION ALL (
			SELECT
				comments.id AS `id`,
				comments.content,
				comments.datecreated,
				comments.star,
				users.displayname,
				users.avatar
			FROM
				comments
			LEFT JOIN users ON users.id = comments.user_id
			WHERE
				comments.post_id      = $_post_id AND
				comments.user_id      = $_user_id AND
				comments.type = 'comment'
			ORDER BY comments.id DESC
			LIMIT 1
			)
			";
		}

		$result = \dash\db::get($query);
		$temp = [];

		if(is_array($result))
		{
			$result = array_map(['\\dash\\app\\comment', 'ready'], $result);

			foreach ($result as $key => $value)
			{
				if(isset($value['id']))
				{
					$temp[$value['id']] = $value;
				}
			}
			$temp = array_values($temp);
		}

		return $temp;
	}


	/**
	 * Determines if rate.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_post_id  The post identifier
	 */
	public static function is_rate($_user_id, $_post_id)
	{
		$query =
		"
			SELECT
				id
			FROM
				comments
			WHERE
				user_id = $_user_id AND
				post_id = $_post_id AND
				type = 'rate'
			LIMIT 1;
		";
		$rate = \dash\db::get($query, 'id', true);
		return $rate;
	}


	/**
	 * save rate to poll
	 *
	 * @param      <type>   $_user_id  The user identifier
	 * @param      <type>   $_post_id  The post identifier
	 * @param      integer  $_rate     The rate
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function rate($_user_id, $_post_id, $_rate)
	{
		$is_rate = self::is_rate($_user_id, $_post_id);
		if($is_rate)
		{
			return true;
		}

		if(intval($_rate) < 0)
		{
			return false;
		}

		if(intval($_rate) > 5)
		{
			$_rate = 5;
		}

		$args =
		[
			'content' => $_rate,
			'type'    => 'rate',
			'status'  => 'approved',
			'user_id'         => $_user_id,
			'post_id'         => $_post_id
		];
		// insert comments
		$result = self::insert($args);

		if($result)
		{

			$total_rate = self::get_total_rate($_post_id);
			if(!$total_rate)
			{
				// insert new value
				$first_meta =
				[
					'total' =>
					[
						'count' => 1,
						'sum'   => $_rate,
						'avg'   => round($_rate / 1, 1)
					],
					"rate$_rate" =>
					[
						'count' => 1,
						'sum'   => $_rate,
						'avg'   => round($_rate / 1, 1)
					]
				];
				$first_meta = json_encode($first_meta, JSON_UNESCAPED_UNICODE);

				$arg =
				[
					'post_id'      => $_post_id,
					'cat'   => "poll_$_post_id",
					'key'   => 'comment',
					'value' => 'rate',
					'meta'  => $first_meta
				];
				return \dash\db\options::insert($arg);
			}
			else
			{
				$id = $total_rate['id'];
				$meta      = json_decode($total_rate['meta'], true);

				if(!is_array($meta))
				{
					return false;
				}

				foreach ($meta as $key => $value)
				{
					if($key == 'total' || $key == "rate$_rate")
					{
						$meta[$key]['count'] = $meta[$key]['count'] + 1;
						$meta[$key]['sum']   = $meta[$key]['sum'] + $_rate;
						$meta[$key]['avg']   = round(floatval($meta[$key]['sum']) / floatval($meta[$key]['count']), 1);
					}
				}
				if(!isset($meta["rate$_rate"]))
				{
					$meta["rate$_rate"] =
					[
						'count' => 1,
						'sum'   => $_rate,
						'avg'   => round($_rate / 1, 1)
					];
				}
				return \dash\db\options::update(['meta' => json_encode($meta, JSON_UNESCAPED_UNICODE)], $id);
			}
		}
	}


	/**
	 * Gets the total rate.
	 *
	 * @param      <type>  $_post_id  The post identifier
	 *
	 * @return     <type>  The total rate.
	 */
	public static function get_total_rate($_post_id)
	{
		$query =
		"
			SELECT
				id,
				meta AS 'meta'
			FROM
				options
			WHERE
				user_id IS NULL AND
				post_id      = $_post_id AND
				cat   = 'poll_$_post_id' AND
				key   = 'comment' AND
				value = 'rate'
			LIMIT 1;
		";
		$result = \dash\db::get($query, null, true);
		return $result;
	}


	/**
	 * Gets all comments for  admin accept
	 *
	 * @param      integer  $_limit  The limit
	 *
	 * @return     <type>   All.
	 */
	public static function admin_get($_limit = 50)
	{
		if(!is_numeric($_limit))
		{
			$_limit = 50;
		}

		$pagenation_query =
		"SELECT	id	FROM comments WHERE	comments.type = 'comment' AND comments.status = 'unapproved'
		 -- comments::admin_get() for pagenation ";
		list($limit_start, $_limit) = \dash\db::pagnation($pagenation_query, $_limit);
		$limit = " LIMIT $limit_start, $_limit ";

		$query =
		"
			SELECT
				comments.*,
				posts.title AS 'title',
				posts.url  AS 'url',
				users.status AS 'status',
				users.email AS 'email'
			FROM
				comments
			INNER JOIN posts ON posts.id = comments.post_id
			INNER JOIN users ON users.id = comments.user_id
			WHERE
				comments.type = 'comment' AND
				comments.status = 'unapproved'
			ORDER BY id ASC
			$limit
			-- comments::admin_get()
		";
		return \dash\db::get($query);
	}


	/**
	 * Sets the comment data.
	 *
	 * @param      <type>  $_comment_id  The comment identifier
	 * @param      <type>  $_type        The type
	 */
	public static function set_comment_data($_comment_id, $_type, $_update = false)
	{
		if($_type != 'minus' && $_type != 'plus')
		{
			return false;
		}

		$set = [];
		$set[] = " $_type = IF($_type IS NULL, 1, $_type + 1) ";

		if($_update)
		{
			$reverse = 'minus';
			if($_type == 'minus')
			{
				$reverse = 'plus';
			}
			$set[] = " $reverse = IF($reverse IS NULL, 0, $reverse - 1) ";
		}
		$set = join($set, ', ');
		$query =
		"
			UPDATE
				comments
			SET
				$set
			WHERE
				id = $_comment_id
		";
		return \dash\db::query($query);
	}
}
?>
