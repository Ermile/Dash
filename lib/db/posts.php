<?php
namespace dash\db;

/** work with posts **/
class posts
{

	use \dash\db\posts\search;

	/**
	 * this library work with posts
	 * v1.0
	 */


	/**
	 * insert new recrod in posts table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		\dash\db\config::public_insert('posts', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * update field from posts table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('posts', ...func_get_args());
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		// get id
		$query = "
				UPDATE  posts
				SET posts.status = 'deleted'
				WHERE posts.id = $_id
				";

		return \dash\db::query($query);
	}


	/**
	 * Gets one record of post
	 *
	 * @param      <type>  $_post_id  The post identifier
	 *
	 * @return     <type>  One.
	 */
	public static function get_one($_post_id)
	{
		$query = "SELECT * FROM posts WHERE id = $_post_id LIMIT 1";
		$result = \dash\db::get($query);
		$result = \dash\utility\filter::meta_decode($result);
		if(isset($result[0]))
		{
			$result = $result[0];
		}
		return $result;
	}


	/**
	 * Gets some identifier.
	 * get some posts by id
	 * @param      <type>   $_ids   The identifiers
	 *
	 * @return     boolean  Some identifier.
	 */
	public static function get_some_id($_ids)
	{
		if(!$_ids)
		{
			return false;
		}

		if(is_array($_ids))
		{
			$_ids = implode(',', $_ids);
		}

		$result = \dash\db::get("SELECT * FROM posts WHERE id IN ($_ids)");
		$result = \dash\utility\filter::meta_decode($result);
		return $result;
	}


	/**
	 * Determines if attachment.
	 *
	 * @param      <type>  $_id    The identifier
	 */
	public static function is_attachment($_id)
	{
		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		$query =
		"
			SELECT * FROM posts
			WHERE id = $_id
			AND type = 'attachment'
			AND posts.status IN ('draft', 'publish')
			LIMIT 1
		";
		$result = \dash\db::get($query, null, true);

		if($result)
		{
			if(isset($result['meta']) && substr($result['meta'], 0,1) === '{')
			{
				$result['meta'] = json_decode($result['meta'], true);
			}
			return $result;
		}
		return false;
	}


	/**
	 * get list of polls
	 * @param  [type] $_user_id set userid
	 * @param  [type] $_return  set return field value
	 * @param  string $_type    set type of post
	 * @return [type]           an array or number
	 */
	public static function get()
	{
		return \dash\db\config::public_get('posts', ...func_get_args());
	}


	public static function get_last_posts($_options = [])
	{
		$date_now = date("Y-m-d H:i:s");

		$limit = "LIMIT $_options[limit]";

		if($_options['pagenation'])
		{
			$pagenation_query =
			"
				SELECT
					COUNT(*) AS `count`
				FROM
					posts
				WHERE
					posts.status      = 'publish' AND
					posts.type        = 'post' AND
					posts.publishdate <= '$date_now'
			";

			$pagenation_query = \dash\db::get($pagenation_query, 'count', true);
			list($limit_start, $limit) = \dash\db::pagnation((int) $pagenation_query, $_options['limit']);
			$limit = " LIMIT $limit_start, $limit ";
		}

		$query =
		"
			SELECT
				*
			FROM
				posts
			WHERE
				posts.status      = 'publish' AND
				posts.type        = 'post' AND
				posts.publishdate <= '$date_now'
			ORDER BY posts.publishdate DESC
			$limit
		";
		return \dash\db::get($query);
	}


	public static function get_posts_term($_options = [], $_type = null)
	{
		$date_now = date("Y-m-d H:i:s");
		$default_options =
		[
			'limit' => 10,
			'cat'   => null,
			'tag'   => null,
			'term'  => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$my_query = null;

		switch ($_type)
		{
			case 'cat':
				if(!$_options['cat'])
				{
					return false;
				}
				$my_query = " terms.slug = '$_options[cat]' AND ";
				break;

			case 'tag':
				if(!$_options['tag'])
				{
					return false;
				}
				$my_query = " terms.slug = '$_options[tag]' AND ";
				break;

			case 'term':
				if(!$_options['term'])
				{
					return false;
				}
				$my_query = " terms.id = '$_options[term]' AND ";
				break;

			default:
				return false;
				break;
		}

		$query =
		"
			SELECT
				posts.*
			FROM
				posts
			WHERE
				posts.id IN
				(
					SELECT
						termusages.related_id
					FROM
						termusages
					INNER JOIN terms ON terms.id = termusages.term_id
					WHERE
						terms.type = '$_type' AND
						$my_query
						termusages.related_id = posts.id
				) AND
				posts.status      = 'publish' AND
				posts.type        = 'post' AND
				posts.publishdate <= '$date_now'
			ORDER BY posts.publishdate DESC
			LIMIT $_options[limit]
		";
		$result = \dash\db::get($query);
		return $result;
	}
}
?>
