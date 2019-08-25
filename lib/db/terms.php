<?php
namespace dash\db;

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
	public static function insert()
	{
		\dash\db\config::public_insert('terms', ...func_get_args());
		return \dash\db::insert_id();
	}


	public static function get_count()
	{
		return \dash\db\config::public_get_count('terms', ...func_get_args());
	}



	/**
	 * insert multi value to terms
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('terms', ...func_get_args());
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('terms', ...func_get_args());
	}


	/**
	 * get the terms by id
	 *
	 * @param      <type>  $_term_id  The term identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('terms', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_title  The title
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_string = null, $_options = [])
	{
		$default_option =
		[
			'public_show_field' => " terms.* , (SELECT COUNT(*) FROM termusages WHERE termusages.term_id = terms.id) AS `count`",
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_option, $_options);

		return \dash\db\config::public_search('terms', $_string, $_options);
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
		$args =
		[
			'caller' => $_caller,
			'limit'  => 1,
		];

		return self::get($args);

	}


	public static function check_multi_id($_ids, $_type, $_subdomain = false)
	{
		if(!is_array($_ids) || !$_type || !$_ids || !$_ids)
		{
			return false;
		}

		$subdomainQuery = null;
		if($_subdomain !== false)
		{
			if($_subdomain)
			{
				$subdomainQuery = " AND terms.subdomain = '$_subdomain' ";
			}
			else
			{
				$subdomainQuery = " AND terms.subdomain IS NULL ";

			}
		}

		$_ids = implode(',', $_ids);

		$query =
		"
			SELECT *
			FROM terms
			WHERE
				terms.id IN ($_ids) AND
				terms.type = '$_type'
				$subdomainQuery
		";
		$result = \dash\db::get($query);

		return $result;

	}


	public static function get_mulit_term_title($_titles, $_type, $_subdomain = false)
	{
		if(!is_array($_titles) || !$_type || !$_titles)
		{
			return false;
		}

		$subdomainQuery = null;
		if($_subdomain !== false)
		{
			if($_subdomain)
			{
				$subdomainQuery = " AND terms.subdomain = '$_subdomain' ";
			}
			else
			{
				$subdomainQuery = " AND terms.subdomain IS NULL ";

			}
		}

		$_titles = implode("','", $_titles);

		$query =
		"
			SELECT *
			FROM terms
			WHERE
				terms.title IN ('$_titles') AND
				terms.type = '$_type'
				$subdomainQuery
		";
		$result = \dash\db::get($query);

		return $result;
	}


	public static function remove($_term_id)
	{
		$query = "SELECT * FROM termusages WHERE termusages.term_id = $_term_id LIMIT 1";
		$check_exist = \dash\db::get($query, null, true);
		if($check_exist)
		{
			return false;
		}

		$query = "DELETE FROM terms WHERE terms.id = $_term_id LIMIT 1";
		return \dash\db::query($query);
	}


	public static function category_count($_lang)
	{
		$query =
		"
			SELECT
				COUNT(*) AS `count`,
				terms.title,
				terms.url
			FROM terms
			INNER JOIN termusages ON termusages.term_id = terms.id
			INNER JOIN posts ON posts.id = termusages.related_id
			WHERE
				terms.language     = '$_lang' AND
				terms.type         = 'cat' AND
				termusages.related = 'posts' AND
				posts.status       = 'publish' AND
				posts.type         = 'post'
			GROUP BY
				terms.id
		";
		$result = \dash\db::get($query);

		return $result;
	}
}
?>
