<?php
namespace lib\db;

/** options managing **/
class options
{
	/**
	 * this library work with options table
	 * v1.0
	 */


	/**
	 * insert new recrod in options table
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
		$query = " INSERT INTO 	options SET $set ";
		\lib\db::query($query);
		return \lib\db::insert_id();
	}


	/**
	 * insert multi record in one query
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value)
		{
			$fields = array_merge($fields, $value);
		}
		// empty record not inserted
		if(empty($fields))
		{
			return true;
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value)
		{
			foreach ($fields as $field_name => $vain)
			{
				if(array_key_exists($field_name, $value))
				{
					$values[] = "'" . $value[$field_name] . "'";
				}
				else
				{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values     = [];
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		// crate string query
		$query = "
				INSERT INTO options
				($fields)
				VALUES
				($values)
				";

		return \lib\db::query($query);

	}


	/**
	 * update record in options table if we have error in insert
	 * get fields and value to update  WHERE fields = $value
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function update_on_error($_args, $_where)
	{
		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		if(!is_array($_args) || !is_array($_where))
		{
			return false;
		}

		$fields = [];
		$where  = [];
		foreach ($_args as $field => $value)
		{
			$fields[] = "$field = '$value'";
		}

		foreach ($_where as $field => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$where[] = " $field LIKE '$value' ";
			}
			elseif($value === null)
			{
				$where[] = " $field IS NULL ";
			}
			else
			{
				$where[] = " $field = '$value' ";
			}
		}

		$set_fields = join($fields, ",");
		$where      = join($where, " AND ");

		// make update fields
		$query = "
				UPDATE
					options
				SET
					$set_fields
				WHERE
					$where
				";

		return \lib\db::query($query);
	}


	/**
	 * update field from options table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($_args as $field => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$query[] = "$field LIKE '$value'";
			}
			else
			{
				$query[] = "$field = '$value'";
			}
		}

		if(empty($query))
		{
			return true;
		}

		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE options
				SET $query
				WHERE options.id = $_id;
				";

		return \lib\db::query($query);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_where_or_id)
	{

		if(is_numeric($_where_or_id))
		{
			$where = " options.id = $_where_or_id ";
		}
		elseif(is_array($_where_or_id))
		{
			$tmp = [];
			foreach ($_where_or_id as $key => $value)
			{
				if(preg_match("/\%/", $value))
				{
					$tmp[] = " $key LIKE '$value' ";
				}
				else
				{
					$tmp[] = " $key = '$value' ";
				}
			}
			$where = join($tmp, " AND ");
		}
		else
		{
			return false;
		}

		$query = " UPDATE options
			SET options.option_status = 'disable'
			WHERE $where -- answers::delete()";
		return \lib\db::query($query);
	}


	/**
	 * real delete record from database
	 *
	 * @param      <type>  $_where_or_id  The where or identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function hard_delete($_where_or_id)
	{
		if(is_numeric($_where_or_id))
		{
			$where = " options.id = $_where_or_id ";
		}
		elseif(is_array($_where_or_id))
		{
			$tmp = [];
			foreach ($_where_or_id as $key => $value)
			{
				if(preg_match("/\%/", $value))
				{
					$tmp[] = " $key LIKE '$value' ";
				}
				else
				{
					$tmp[] = " $key = '$value' ";
				}
			}
			$where = join($tmp, " AND ");
		}
		else
		{
			return false;
		}

		$query = " DELETE FROM	options	WHERE $where -- answers::hard_delete() ";
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
	 * get the record of option table
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		$only_one_recort = false;

		if(empty($_args) || !is_array($_args))
		{
			return false;
		}

		if(isset($_args['limit']))
		{
			if($_args['limit'] == 1)
			{
				$only_one_recort = true;
			}

			$limit = "LIMIT ". $_args['limit'];
			unset($_args['limit']);
		}
		else
		{
			$limit = null;
		}

		$where = [];
		foreach ($_args as $key => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$where[] = " options.$key LIKE '$value'";
			}
			elseif($value === null)
			{
				$where[] = " options.$key IS NULL";
			}
			elseif(is_numeric($value))
			{
				$where[] = " options.$key = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " options.$key = '$value'";
			}
		}
		$where = "WHERE ". join($where, " AND ");

		$parent_id = null;

		if(\lib\db::check_version('>=', '1.1.0', 'addons'))
		{
			$parent_id = ", parent_id";
		}

		$query =
		"
			SELECT
				id,
				user_id 		AS 'user_id',
				post_id 		AS 'post_id',
				option_cat 		AS 'cat',
				option_key 		AS 'key',
				option_value 	AS 'value',
				option_meta 	AS 'meta',
				option_status 	AS 'status',
				date_modified
				$parent_id
			FROM
				options
			$where
			$limit
		";
		$result = \lib\db::get($query, null, $only_one_recort);
		if(isset($result['meta']) && substr($result['meta'], 0, 1) == '{')
		{
			$result['meta'] = json_decode($result['meta'], true);
		}
		return $result;
	}


	/**
	 * update the option record  option_value++
	 *
	 * @param      <type>  $_where  The where
	 * @param      string  $_field  The field
	 */
	private static function plus_meta($_where, $_plus = 1, $_type = 'plus')
	{
		if(!is_array($_where))
		{
			return false;
		}

		$args = [];
		foreach ($_where as $key => $value)
		{
			if($value === null)
			{
				$args[] = " options.$key = NULL ";
			}
			elseif(is_string($value))
			{
				$args[] = " options.$key  = '$value' ";
			}
			elseif(is_numeric($value))
			{
				$args[] = " options.$key  = $value ";
			}
		}

		if(empty($args))
		{
			return false;
		}


		$update_meta_query = "IF(options.option_meta IS NULL OR options.option_meta = '', $_plus, options.option_meta + $_plus)";
		if($_type === 'minus')
		{
			$update_meta_query = "IF(options.option_meta IS NULL OR options.option_meta = '' OR options.option_meta = 0, $_plus, options.option_meta - $_plus)";
		}

		$args = join($args, " , ");

		$query =
		"
			INSERT INTO options
			SET
				$args,
				options.option_meta   = $_plus,
				options.option_status = 'enable'
			ON DUPLICATE KEY UPDATE
				$args,
				options.option_meta   = $update_meta_query,
				options.option_status = 'enable'

		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * plus options meta
	 *
	 * @param      <type>  $_where  The where
	 * @param      <type>  $_plus   The plus
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function plus($_where, $_plus = 1)
	{
		return self::plus_meta($_where, $_plus, 'plus');
	}



	/**
	 * minus the option meta
	 *
	 * @param      <type>  $_where  The where
	 * @param      <type>  $_minus  The minus
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function minus($_where, $_minus = 1)
	{
		return self::plus_meta($_where, $_minus, 'minus');
	}
}
?>