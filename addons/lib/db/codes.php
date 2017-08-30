<?php
namespace lib\db;

/** codes managing **/
class codes
{

	/**
	 * set new code
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function set($_args)
	{
		$default_args =
		[
			'code'    => null,
			'type'    => null,
			'related' => null,
			'id'      => null,
			'creator' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['code'] || !$_args['related'] || !$_args['id'] || !$_args['type'])
		{
			return false;
		}

		if(mb_strlen($_args['code']) > 90)
		{
			return false;
		}

		$check_exist_code =
		[
			'type'  => $_args['type'],
			'slug'  => $_args['code'],
			'limit' => 1,
		];

		$check_exist_code = \lib\db\terms::get($check_exist_code);

		if(isset($check_exist_code['id']))
		{
			$term_id = $check_exist_code['id'];
		}
		else
		{

			$insert_term =
			[
				'type'    => $_args['type'],
				'slug'    => $_args['code'],
				'user_id' => $_args['creator'],
				'status'  => 'enable',
			];
			$term_id = \lib\db\terms::insert($insert_term);
		}

		if(!$term_id)
		{
			return false;
		}

		$check_exist_usage =
		[
			'term_id'    => $term_id,
			'related'    => $_args['related'],
			'related_id' => $_args['id'],
			'limit'      => 1,
		];

		$check_exist_usage = \lib\db\termusages::get($check_exist_usage);

		if(!$check_exist_usage)
		{
			$insert_termusage =
			[
				'term_id'    => $term_id,
				'related'    => $_args['related'],
				'related_id' => $_args['id'],
			];
			\lib\db\termusages::insert($insert_termusage);
		}
		elseif(isset($check_exist_usage['status']) && $check_exist_usage['status'] !== 'enable')
		{
			$where =
			[
				'term_id'    => $term_id,
				'related'    => $_args['related'],
				'related_id' => $_args['id'],
			];

			$set = ['status' => 'enable'];

			\lib\db\termusages::update($where, $set);
		}
		else
		{
			return false;
		}

		return true;
	}


	/**
	 * get if exist code
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function get($_args)
	{
		$default_args =
		[
			'type'    => null,
			'related' => null,
			'id'      => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['related'] || !$_args['id'] || !is_numeric($_args['id']))
		{
			return false;
		}

		$type_query = null;

		if($_args['type'] && is_string($_args['type']))
		{
			$type_query = " AND terms.type = '$_args[type]' ";
		}

		$query =
		"
			SELECT
				termusages.*,
				terms.*,
				terms.status AS `term_status`
			FROM
				termusages
			INNER JOIN terms ON terms.id = termusages.term_id
			WHERE
				termusages.related = '$_args[related]' AND
				termusages.related_id = $_args[id]
				$type_query
		";
		return \lib\db::get($query)
	}



	/**
	 * get if exist code
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function remove($_args)
	{
		$default_args =
		[
			'code'    => null,
			'type'    => null,
			'related' => null,
			'id'      => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		if(!$_args['related'] || !$_args['id'] || !is_numeric($_args['id']))
		{
			return false;
		}
	}
}
?>