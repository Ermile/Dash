<?php
namespace dash\db;


/** contacts managing **/
class contacts
{

	/**
	 * insert new tag in contacts table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		\dash\db\config::public_insert('contacts', ...func_get_args());
	}


	/**
	 * insert multi value to contacts
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi()
	{
		return \dash\db\config::public_multi_insert('contacts', ...func_get_args());
	}


	/**
	 * update field from contacts table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{
		if(is_array($_args) && is_numeric($_id))
		{
			$set = [];
			foreach ($_args as $key => $value)
			{
				if(isset($value) && $value != '')
				{
					$set[] = "contacts.$key = '$value' ";
				}
				else
				{
					$set[] = "contacts.$key = NULL ";
				}
			}

			if(!empty($set))
			{
				$set = implode(',', $set);
				$query = "UPDATE contacts SET $set WHERE contacts.id = $_id LIMIT 1";
				return \dash\db::query($query);
			}
		}

		return null;
	}


	/**
	 * update record by where condition
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update_where()
	{
		return \dash\db\config::public_update_where('contacts', ...func_get_args());
	}


	/**
	 * get the contacts by id
	 *
	 * @param      <type>  $_contact_id  The contact identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('contacts', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_title  The title
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \dash\db\config::public_search('contacts', ...func_get_args());
	}
}
?>
