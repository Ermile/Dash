<?php
namespace lib\db;

/** termusage managing **/
class termusages
{
	/**
	 * this library work with termusages
	 * v1.0
	 */


	/**
	 * insert new tag in termusages table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
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
			elseif(is_string($value))
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');

		$query =
		"
			INSERT INTO
				termusages
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check($_args)
	{
		if(!isset($_args['term_id']))
		{
			return false;
		}

		if(!isset($_args['termusage_foreign']))
		{
			return false;
		}

		if(!isset($_args['termusage_id']))
		{
			return false;
		}

		$query =
		"
			SELECT
				*
			FROM
				termusages
			WHERE
				term_id           = $_args[term_id] AND
				termusage_id      = $_args[termusage_id] AND
				termusage_foreign = '$_args[termusage_foreign]'
			LIMIT 1
		";
		return \lib\db::get($query, null, true);
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>   $_old   The old
	 * @param      <type>   $_new   The new
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function update($_old, $_new)
	{
		$set = \lib\db\config::make_set($_new);
		$where = \lib\db\config::make_where($_old);

		$query =
		"
			UPDATE
				termusages
			SET
				$set
			WHERE
				$where
			LIMIT 1
		";
		return \lib\db::query($query);
	}
	/**
	 * insert mutli tags (get id of tags) to teruseage
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args, $_options = [])
	{
		if(empty($_args))
		{
			return false;
		}

		$default_options = ['ignore' => false];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value)
		{
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values   = [];
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
			$values = [];
		}

		if(empty($fields))
		{
			return null;
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		$ignore = null;
		if($_options['ignore'])
		{
			$ignore = "IGNORE";
		}
		// crate string query
		$query = "INSERT $ignore INTO termusages ($fields) VALUES ($values) ";
		return \lib\db::query($query);
	}


	public static function usage($_usageid, $_foreign = 'posts', $_type = null)
	{
		if($_foreign === null)
		{
			$_foreign = 'posts';
		}
		$type = null;
		if($_type)
		{
			$type = " AND terms.term_type = '$_type' ";
		}
		$query =
		"
			SELECT
				*
			FROM
				terms
			INNER JOIN termusages ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = '$_foreign' AND
				termusages.termusage_id      = $_usageid
				$type
		";
		return \lib\db::get($query);
	}


	/**
	 * remove record
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function remove($_args, $_term_type = 'tag')
	{
		if(!is_array($_args))
		{
			return false;
		}

		$default_args =
		[
			'term_id'           => false,
			'termusage_id'      => false,
			'termusage_foreign' => false
		];

		$_args = array_merge($default_args, $_args);

		if(!$_args['termusage_foreign'] || !$_args['termusage_id'])
		{
			return false;
		}

		$query =
		"
			DELETE termusages.* FROM termusages
			INNER JOIN terms ON terms.id = termusages.term_id
			WHERE
				termusages.termusage_foreign = '$_args[termusage_foreign]' AND
				termusages.termusage_id = $_args[termusage_id] AND
				terms.term_type = '$_term_type'
		";

		if($_args['term_id'])
		{
			$query .= " AND termusages.term_id = $_args[term_id] ";
		}
		return \lib\db::query($query);
	}
}
?>