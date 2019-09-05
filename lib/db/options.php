<?php
namespace dash\db;

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
	public static function insert()
	{
		\dash\db\config::public_insert('options', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * insert multi record in one query
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function insert_multi()
	{
		return \dash\db\config::public_multi_insert('options', ...func_get_args());
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
		$set_fields = \dash\db\config::make_set($_args);
		$where      = \dash\db\config::make_where($_where);
		if(!$_args || !$_where)
		{
			return false;
		}

		// make update fields
		$query = "UPDATE options SET $set_fields	WHERE $where ";
		return \dash\db::query($query);
	}


	/**
	 * update field from options table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('options', ...func_get_args());
	}


	public static function get_count()
	{
		return \dash\db\config::public_get_count('options', ...func_get_args());
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
			$where = \dash\db\config::make_where($_where_or_id);
		}
		else
		{
			return false;
		}

		$query = " UPDATE options  SET options.status = 'disable' WHERE $where ";
		return \dash\db::query($query);
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
			$where = \dash\db\config::make_where($_where_or_id);
		}
		else
		{
			return false;
		}

		$query = " DELETE FROM	options	WHERE $where ";
		return \dash\db::query($query);
	}


	/**
	 * get the record of option table
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function get()
	{
		$result = \dash\db\config::public_get('options', ...func_get_args());
		if(isset($result['meta']) && substr($result['meta'], 0, 1) == '{')
		{
			$result['meta'] = json_decode($result['meta'], true);
		}
		return $result;
	}


	/**
	 * update the option record  value++
	 *
	 * @param      <type>  $_where  The where
	 * @param      string  $_field  The field
	 */
	private static function plus_value($_where, $_plus_minue = 1, $_type = 'plus')
	{
		$where = \dash\db\config::make_where($_where);
		$set = \dash\db\config::make_set($_where);
		if(!$where || !$set)
		{
			return false;
		}

		$update_value_query = "IF(options.value IS NULL OR options.value = '', $_plus_minue, options.value + $_plus_minue)";
		if($_type === 'minus')
		{
			$update_value_query = "IF(options.value IS NULL OR options.value = '' OR options.value = 0, $_plus_minue, options.value - $_plus_minue)";
		}

		$query =
		"
			INSERT INTO options
			SET
				$set,
				options.value   = $_plus_minue,
				options.status = 'enable'
			ON DUPLICATE KEY UPDATE
				$set,
				options.value   = $update_value_query,
				options.status = 'enable'

		";
		$result = \dash\db::query($query);
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
		return self::plus_value($_where, $_plus, 'plus');
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
		return self::plus_value($_where, $_minus, 'minus');
	}
}
?>
