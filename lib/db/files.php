<?php
namespace dash\db;


class files
{

	public static function insert($_args)
	{
		\dash\db\config::public_insert('files', $_args);
		return \dash\db::insert_id();
	}


	public static function attachment_count()
	{
		$query = "SELECT COUNT(*) AS 'count' FROM files ";
		$count = \dash\db::get($query,'count', true);
		return $count;
	}

	/**
	 * check duplocate MD5 of file in database
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function duplicate($_md5)
	{

		$qry_count = "SELECT * FROM files WHERE files.md5 = '$_md5' LIMIT 1";
		$qry_count = \dash\db::get($qry_count, null, true);
		if($qry_count && !empty($qry_count))
		{
			\dash\temp::set('upload', ["id" =>  $qry_count['id'], 'url' => $qry_count['path'], 'size' => $qry_count['size']]);
			return $qry_count;
		}
		return false;
	}


	public static function get_by_ids($_ids)
	{
		$query = "SELECT * FROM files WHERE files.id IN ($_ids) ";
		$result = \dash\db::get($query);
		return $result;
	}


	public static function search()
	{
		$result = \dash\db\config::public_search('files', ...func_get_args());
		return $result;
	}

	public static function get()
	{
		$result = \dash\db\config::public_get('files', ...func_get_args());
		return $result;
	}
}
?>