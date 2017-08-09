<?php
namespace lib\db;

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
	public static function insert($_args){

		$set = [];
		foreach ($_args as $key => $value) {
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_int($value))
			{
				$set[] = " `$key` = $value ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');
		$query =
		"
			INSERT INTO
				comments
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * update field from comments table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id) {

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($_args as $field => $value) {
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE comments
				SET $query
				WHERE comments.id = $_id;
				";

		return \lib\db::query($query);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id) {
		// get id
		$query = "
				UPDATE comments
				SET comments.comment_status = 'deleted'
				WHERE comments.id = $_id
				";

		return \lib\db::query($query);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query') {
		return \lib\db::$_type($_query);
	}




	public static function save($_content, $_args = null)
	{
		$values =
		[
			"post_id"            => null,
			"comment_author"     => null,
			"comment_email"      => null,
			"comment_url"        => null,
			// "comment_content" => null,
			"comment_meta"       => null,
			"comment_status"     => null,
			"comment_parent"     => null,
			"user_id"            => null,
			"visitor_id"         => null,
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
			$newKey = 'comment_'. $key;
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
		$values['comment_content'] = "'". htmlspecialchars($_content). "'";
		// set comment status if not set
		if(!isset($values['comment_status']))
		{
			$values['comment_status'] = "'unapproved'";
		}
		// set time of comment
		if(isset($values['comment_meta']) && is_array($values['comment_meta']))
		{
			$values['comment_meta']['time'] = date('Y-m-d H:i:s');
		}
		else
		{
			$values['comment_meta'] = ['time' => date('Y-m-d H:i:s')];
		}
		$values['comment_meta'] = "'".json_encode($values['comment_meta'], JSON_UNESCAPED_UNICODE)."'";
		// generate query text
		$list_field  = array_keys($values);
		$list_field  = implode($list_field, ', ');
		$list_values = implode($values, ', ');
		// create query string
		$qry       = "INSERT INTO comments ( $list_field ) VALUES ( $list_values )";
		var_dump($qry);
		// run query and insert into db
		$result    = \lib\db::query($qry);
		// get insert id
		$commentId = \lib\db::insert_id();
		// return last insert id
		return $commentId;
	}


	public static function get_post_comment($_post_id, $_limit = 6, $_user_id = false)
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
				*
			FROM
				comments
			WHERE
				comments.post_id        = $_post_id AND
				comments.comment_status = 'approved' AND
				comments.comment_type   = 'comment'
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
				*
			FROM
				comments
			WHERE
				comments.post_id      = $_post_id AND
				comments.user_id      = $_user_id AND
				comments.comment_type = 'comment'
			ORDER BY comments.id DESC
			LIMIT 1
			)
			";
		}

		return self::select($query,"get");
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
				comment_type = 'rate'
			LIMIT 1;
		";
		$rate = \lib\db::get($query, 'id', true);
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
			'comment_content' => $_rate,
			'comment_type'    => 'rate',
			'comment_status'  => 'approved',
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
					'option_cat'   => "poll_$_post_id",
					'option_key'   => 'comment',
					'option_value' => 'rate',
					'option_meta'  => $first_meta
				];
				return \lib\db\options::insert($arg);
			}
			else
			{
				$option_id = $total_rate['id'];
				$meta      = json_decode($total_rate['meta'], true);

				if(!is_array($meta))
				{
					return false;
				}

				foreach ($meta as $key => $value) {
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
				return \lib\db\options::update(['option_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE)], $option_id);
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
				option_meta AS 'meta'
			FROM
				options
			WHERE
				user_id IS NULL AND
				post_id      = $_post_id AND
				option_cat   = 'poll_$_post_id' AND
				option_key   = 'comment' AND
				option_value = 'rate'
			LIMIT 1;
		";
		$result = \lib\db::get($query, null, true);
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
		"SELECT	id	FROM comments WHERE	comments.comment_type = 'comment' AND comments.comment_status = 'unapproved'
		 -- comments::admin_get() for pagenation ";
		list($limit_start, $_limit) = \lib\db::pagnation($pagenation_query, $_limit);
		$limit = " LIMIT $limit_start, $_limit ";

		$query =
		"
			SELECT
				comments.*,
				posts.post_title AS 'title',
				posts.post_url  AS 'url',
				users.user_status AS 'status',
				users.user_email AS 'email'
			FROM
				comments
			INNER JOIN posts ON posts.id = comments.post_id
			INNER JOIN users ON users.id = comments.user_id
			WHERE
				comments.comment_type = 'comment' AND
				comments.comment_status = 'unapproved'
			ORDER BY id ASC
			$limit
			-- comments::admin_get()
		";
		return \lib\db::get($query);
	}
}
?>