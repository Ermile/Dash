<?php
namespace lib\db;

/** work with posts **/
class posts
{

	use posts\search;

	/**
	 * this library work with posts
	 * v1.0
	 */


	/**
	 * insert new recrod in posts table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}

		$set = [];
		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_array($value))
			{
				$set[] = " `$key` = '".json_encode($value, JSON_UNESCAPED_UNICODE)."' ";
			}
			elseif(is_string($value))
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');
		$query =
		"
			INSERT INTO
				posts
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * update field from posts table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		if(!is_array($_args))
		{
			return false;
		}

		$query = [];
		foreach ($_args as $field => $value)
		{
			if(is_numeric($value))
			{
				$query[] = " $field = $value ";
			}
			elseif ($value === null)
			{
				$query[] = " $field = NULL ";
			}
			else
			{
				$query[] = " $field = '$value' ";
			}
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE posts
				SET $query
				WHERE posts.id = $_id;
				";

		return \lib\db::query($query);
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
				SET posts.post_status = 'deleted'
				WHERE posts.id = $_id
				";

		return \lib\db::query($query);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return \lib\db::$_type($_query);
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
		$result = \lib\db::get($query);
		$result = \lib\utility\filter::meta_decode($result);
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

		$result = \lib\db::get("SELECT * FROM posts WHERE id IN ($_ids)");
		$result = \lib\utility\filter::meta_decode($result);
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
			AND post_type = 'attachment'
			AND posts.post_status IN ('draft', 'publish')
			LIMIT 1
		";
		$result = \lib\db::get($query, null, true);

		if($result)
		{
			if(isset($result['post_meta']) && substr($result['post_meta'], 0,1) === '{')
			{
				$result['post_meta'] = json_decode($result['post_meta'], true);
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
	public static function get($_user_id = null, $_return = null, $_type = 'post')
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE NOT NULL";
		}
		else
		{
			$_type = "post_type = '". $_type. "'";
		}
		// calc user id if exist
		if($_user_id)
		{
			$_user_id = "AND user_id = $_user_id";
		}
		else
		{
			$_user_id = null;
		}
		// generate query string
		$qry = "SELECT * FROM posts WHERE $_type $_user_id";
		// run query
		if($_return && $_return !== 'count')
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === 'count')
		{
			return count($result);
		}
		// return last insert id
		return $result;
	}


	/**
	 * save question into post table
	 * @param  [type] $_title    [description]
	 * @param  [type] $_answersList [description]
	 * @return [type]               [description]
	 */
	public static function insertOrder($_title, $_meta, $_user_id = null)
	{
		$slug    = \lib\utility\filter::slug($_title);
		$pubDate = date('Y-m-d H:i:s');
		$url     = 'order/'.date('Y-m-d').$_user_id.'/'.$slug;
		$_meta   = json_encode($_meta, JSON_UNESCAPED_UNICODE);
		// create query string
		$qry = "INSERT INTO posts
		(
			`post_language`,
			`post_title`,
			`post_slug`,
			`post_url`,
			`post_meta`,
			`post_type`,
			`post_status`,
			`post_publishdate`,
			`user_id`
		)
		VALUES
		(
			'fa',
			'$_title',
			'$slug',
			'$url',
			'$_meta',
			'order',
			'draft',
			'$pubDate',
			$_user_id
		)";
		// run query
		$result        = \lib\db::query($qry);
		// return last insert id
		$newId    = \lib\db::insert_id();
		// save answers into options table
		return $newId;
	}


	/**
	 * Gets the post meta.
	 *
	 * @param      <type>  $_post_id  The post identifier
	 *
	 * @return     <type>  The post meta.
	 */
	public static function get_post_meta($_post_id)
	{
		$query =
		"
			SELECT
				*
			FROM
				options
			WHERE
				options.post_id = $_post_id AND
				options.option_cat = 'poll_$_post_id' AND
				options.user_id IS NULL AND
				options.option_status = 'enable'

		";
		$result = \lib\db\options::select($query, "get");
		return \lib\utility\filter::meta_decode($result);
	}
}
?>