<?php
namespace lib\db;
use \lib\utility\location\languages;

/** terms managing **/
class terms
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * insert new tag in terms table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args, $_multi_insert = false)
	{
		if(!is_array($_args))
		{
			return false;
		}

		if($_multi_insert)
		{
			foreach ($_args as $key => $value)
			{
				self::insert($value);
			}
			return true;
		}

		$title = null;
		if(isset($_args['term_title']))
		{
			$title = $_args['term_title'];
		}

		$url = null;
		if(isset($_args['term_url']))
		{
			$url = $_args['term_url'];
		}

		$slug = null;
		if(isset($_args['term_slug']))
		{
			$slug = $_args['term_slug'];
		}
		else
		{
			$slug = \lib\utility\filter::slug($title);
		}

		if($title && !$slug)
		{
			$slug = $title;
		}

		if(!$slug)
		{
			\lib\debug::error(T_("term_slug not found"), 'term_slug', 'arguments');
			return false;
		}
		else
		{
			$_args['term_slug'] = $slug;
		}

		if(!$url)
		{
			\lib\debug::error(T_("term_url not found"), 'term_url', 'arguments');
			return false;
		}

		$language    = null;
		$must_insert = [];

		if(isset($_args['term_language']))
		{
			if(languages::check($_args['term_language']))
			{
				$language = $_args['term_language'];
			}
			else
			{
				unset($_args['term_language']);
			}
		}

		$check_exist = self::exists($url, $language);

		if($check_exist)
		{
			return $check_exist;
		}

		if(empty($_args))
		{
			return null;
		}


		$set = [];
		foreach ($_args as $key => $value)
		{
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

		$query = " INSERT INTO terms SET $set";

		\lib\db::query($query);
		return \lib\db::insert_id();
	}


	/**
	 * check terms url and lnguage
	 *
	 * @param      <type>  $_url       The url
	 * @param      <type>  $_language  The language
	 */
	private static function exists($_url, $_language)
	{
		if($_language === null)
		{
			$language = "term_language IS NULL";
		}
		else
		{
			$language = "term_language = '$_language' ";
		}

		$query  = "SELECT id FROM terms WHERE term_url = '$_url' AND $language LIMIT 1";
		$result = \lib\db::get($query, 'id', true);
		return $result;
	}


	/**
	 * insert multi value to terms
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		return self::insert($_args, true);

		// // marge all input array to creat list of field to be insert
		// $fields = [];
		// foreach ($_args as $key => $value)
		// {
		// 	$fields = array_merge($fields, $value);
		// }

		// // creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		// $values   = [];
		// $together = [];
		// foreach ($_args	 as $key => $value)
		// {
		// 	foreach ($fields as $field_name => $vain)
		// 	{
		// 		if(array_key_exists($field_name, $value)){
		// 			$values[] = "'" . trim($value[$field_name]) . "'";
		// 		}else{
		// 			$values[] = "NULL";
		// 		}
		// 	}
		// 	$together[] = join($values, ",");
		// 	$values = [];
		// }

		// if(empty($fields))
		// {
		// 	return null;
		// }

		// $fields = join(array_keys($fields), ",");

		// $values = join($together, "),(");

		// // crate string query
		// $query = "
		// 		INSERT IGNORE INTO terms
		// 		($fields)
		// 		VALUES
		// 		($values)
		// 		";

		// return \lib\db::query($query);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		$query = [];
		foreach ($_args as $field => $value)
		{
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE terms
				SET $query
				WHERE terms.id = $_id;
				";

		return \lib\db::query($query);

	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function block($_id)
	{

		// get id
		$query = "
				UPDATE FROM terms
				SET terms.term_status = 'block'
				WHERE terms.id = $_id
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
	 * get terms list used in specefic table
	 * @author Javad Evazzade
	 * @param  [type] $_usageid [description]
	 * @param  string $_return  [description]
	 * @param  string $_foreign [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function usage($_usageid, $_return = 'term_title', $_foreign = 'posts', $_type = 'tag')
	{
		if($_return === null)
		{
			$_return = 'term_title';
		}
		if($_foreign === null)
		{
			$_foreign = 'posts';
		}

		if($_type === null)
		{
			$_type = 'tag';
		}

		$type_query = " terms.term_type = '$_type' ";

		if(preg_match("/\%/", $_type))
		{
			$type_query = " terms.term_type LIKE '$_type' ";
		}

		$qry ="SELECT * FROM terms
		INNER JOIN termusages
			ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = '$_foreign' AND
				termusages.termusage_id = $_usageid AND
				$type_query
		";
		// run query
		if($_return && $_return !== true)
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === true)
		{
			$result = count($result);
		}
		// return final result
		return $result;
	}


	/**
	 * get the terms by id
	 *
	 * @param      <type>  $_term_id  The term identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_term_id, $_field = null)
	{
		$field     = '*';
		$get_field = null;
		if(is_array($_field))
		{
			$field     = '`'. join($_field, '`, `'). '`';
			$get_field = null;
		}
		elseif($_field && is_string($_field))
		{
			$field     = '`'. $_field. '`';
			$get_field = $_field;
		}

		$query =
		"
			SELECT
				$field
			FROM
				terms
			WHERE
				terms.id = $_term_id
			LIMIT 1
			-- terms::get()
		";
		$result = \lib\db::get($query, $get_field, true);
		return $result;
	}


	/**
	 * Gets the multi record of terms table
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  The multi.
	 */
	public static function get_multi($_args = [])
	{
		$limit = null;
		if(isset($_args['limit']) && $_args['limit'] === 1)
		{
			$limit = " LIMIT 1 ";
		}
		unset($_args['limit']);

		$where = [];
		foreach ($_args as $key => $value)
		{
			$where[] = " terms.$key = '". $value. "' ";
		}
		if(empty($where))
		{
			return false;
		}

		$where = join($where , " AND ");

		$query = "SELECT * FROM terms WHERE $where $limit";

		if($limit)
		{
			return \lib\db::get($query, null, true);
		}
		else
		{
			return \lib\db::get($query);
		}

	}


	/**
	 * get some id of terms table to insert in termusages table
	 *
	 * @param      <type>   $_terms  The terms
	 * @param      <type>   $_type   The type
	 *
	 * @return     boolean  The multi identifier.
	 */
	public static function get_multi_id($_terms, $_type = null)
	{
				//split terms
		if(is_array($_terms))
		{
			$terms = $_terms;
		}
		else
		{
			$terms = preg_split("/\,/", $_terms);
		}
		if(!is_array($terms))
		{
			return false;
		}
		// trim all value
		foreach ($terms as $key => $value)
		{
			$terms[$key] = trim($value);
		}
		// remove empty terms
		$terms = array_filter($terms);

		if(empty($terms))
		{
			return null;
		}

		$condition = [];
		foreach ($terms as $key => $value)
		{
			$condition[] = " term_title = '" . $value . "' ";
		}

		$condition = join($condition, " OR ");

		$type = null;
		if($_type)
		{
			$type = " term_type = '$_type' AND ";
		}

		$query = "
			SELECT
				id
			FROM
				terms
			WHERE
				$type
				($condition)
			";

		$result = \lib\db::get($query, "id");
		return $result;
	}


	/**
	 * Gets the identifier of terms table
	 *
	 * @param      <type>  $_term_title  The term title
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_id($_term_title, $_type = null)
	{

		$type = null;
		if($_type)
		{
			$type = " term_type = '$_type' AND ";
		}

		$query = "
			SELECT
				id
			FROM
				terms
			WHERE
				$type
				term_title = '$_term_title'
			LIMIT 1
			";

		$result = \lib\db::get($query, "id", true);
		return $result;
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_title  The title
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_title, $_options = [])
	{
		$default_options =
		[
			'term_type'   => 'tag',
			'start_limit' => 0,
			'end_limit'   => 10,
			'limit'       => 10,
			'pagenation'  => false,
			'parent'      => null,
			'order' 	  => "ASC",
			'status'      => [],
		];

		$_options = array_merge($default_options, $_options);

		$term_type = " terms.term_type = '$_options[term_type]' ";

		if(preg_match("/\%/", $_options['term_type']))
		{
			$term_type = " terms.term_type LIKE '$_options[term_type]' ";
		}

		$status = null;
		if(!empty($_options['status']))
		{
			$status    = implode("','", $_options['status']);
			$status = " terms.term_status IN ('$status') AND ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		if($_options['pagenation'] === true)
		{
			$pagenation_query =
			"SELECT	id FROM terms WHERE
			terms.term_type = '$_options[term_type]' AND
			terms.term_title LIKE '%$_title%' ";
			list($limit_start, $limit) = \lib\db::pagnation($pagenation_query, $_options['limit']);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			$limit = " LIMIT $start_limit, $end_limit ";
		}

		$parent_condition = " IS NULL ";
		if($_options['parent'])
		{
			$parent_condition = " = ". (int) $_options['parent'];
		}
		// terms.term_parent $parent_condition 	 AND
		$query =
		"
			SELECT
				terms.id 					AS `id`,
				terms.term_title 			AS `title`,
				IFNULL(terms.term_count, 0) AS `count`,
				terms.term_url 				AS `url`,
				terms.term_desc 			AS `desc`,
				terms.term_parent 			AS `parent`,
				terms.term_meta 			AS `meta`
			FROM
				terms
			WHERE
				$status
				$term_type AND
				(
					terms.term_title LIKE '%$_title%' OR
					terms.term_meta  LIKE '%$_title%'
				)
			ORDER BY `id` $_options[order]
			$limit
		";
		return \lib\db::get($query);
	}


	/**
	 * get the terms by caller field
	 *
	 * @param      <type>   $_caller  The caller
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function caller($_caller)
	{
		$query = "SELECT * FROM terms WHERE term_caller = '$_caller' LIMIT 1";
		$result = \lib\db::get($query, null, true);
		if(!$result || empty($result))
		{
			return false;
		}
		return $result;

	}
}
?>