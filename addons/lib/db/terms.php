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
		return \lib\db\config::public_update('terms', ...func_get_args());
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
		return \lib\db\config::public_get('terms', ...func_get_args());
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
	public static function search()
	{
		return \lib\db\config::public_search('terms', ...func_get_args());
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