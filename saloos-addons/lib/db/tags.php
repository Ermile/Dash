<?php
namespace lib\db;

/** terms managing **/
class tags
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * insert new tag in terms table
	 * tag is a record of terms
	 * so we set the term_type and send $_arg to terms::insert funciton
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		// jost tag can insert
		$_args['term_type']	= 'tag';

		return terms::insert($_args);
	}


	/**
	 * insert mulit tag
	 *
	 * @param      <type>  $_tags  strign of tags
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */

	public static function insert_multi($_tags)
	{

		if(is_string($_tags))
		{
			$tags = preg_split("/\,/", $_tags);
		}
		elseif(is_array($_tags))
		{
			$tags = $_tags;
		}
		else
		{
			return false;
		}
		//split tags
		// trim all value
		foreach ($tags as $key => $value)
		{
			$tags[$key] = trim($value);
		}
		// remove empty tags
		$tags = array_filter($tags);

		if(empty($tags))
		{
			return null;
		}

		$result = [];
		foreach ($tags as $key => $value)
		{
			$result[] =
			[
				'term_type'  => 'tag',
				'term_title' => $value,
				'term_url'   => $value,
				'term_slug'  => \lib\utility\filter::slug($value)
			];
		}
		return \lib\db\terms::insert_multi($result);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		// jost tag can insert
		$_args['term_type']	= 'tag';

		return terms::update($_args, $_id);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		return terms::delete($_id);
	}


	/**
	 * get id
	 *
	 * @param      <type>  $_tags  The tags
	 */
	public static function get_multi_id($_tags)
	{
		return \lib\db\terms::get_multi_id($_tags, 'tag');
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return terms::select($_query, $_type);
	}


	/**
	 * get tags list used in specefic post in specefic table
	 * @author Javad Evazzadeh
	 * @param  [type]  $_post_id   [description]
	 * @param  [type]  $_return    [description]
	 * @param  [type]  $_foreign   [description]
	 * @param  boolean $_seperator [description]
	 * @return [type]              [description]
	 */
	public static function usage($_post_id, $_return = null, $_foreign = null, $_seperator = false)
	{
		$result = terms::usage($_post_id, $_return, $_foreign, 'tag');
		if($_seperator)
		{
			if(is_array($result) && $result)
			{
				if($_seperator === true)
				{
					$_seperator = ', ';
				}
				$result = implode($result, $_seperator);
			}
			else
			{
				$result = "";
			}
		}
		return $result;
	}


	/**
	 * Gets the post similar.
	 * default get the post id and return similar post
	 * you can set the poll is null and send list of tags in array or string of tags has splitable by ','
	 * and get post similar this tags
	 *
	 * @param      <type>  $_post_id  The post identifier
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  The post similar.
	 */
	public static function get_post_similar($_post_id, $_options = [])
	{
		if(!$_post_id)
		{
			return false;
		}

		$default_options =
		[
			'limit'             => 5,
			'term_type'         => 'tag',
			'termusage_foreign' => 'posts',
			'post_privacy'      => null,
			'post_status'       => null,
		];

		$_options = array_merge($default_options, $_options);

		$where = [];
		if($_options['post_privacy'])
		{
			$where[] = " posts.post_privacy = '$_options[post_privacy]' ";
		}

		if($_options['post_status'])
		{
			$where[] = " posts.post_status = '$_options[post_status]' ";
		}

		if(!empty($where))
		{
			$where = implode(' AND ', $where) . " AND ";
		}
		else
		{
			$where = null;
		}

		if(!is_numeric($_options['limit']))
		{
			$_options['limit'] = 5;
		}

		$query =
		"
			SELECT
				posts.id 			AS 'id',
				posts.post_title 	AS 'title',
				posts.post_url 		AS 'url'
			FROM
				termusages
			INNER JOIN posts ON posts.id = termusages.termusage_id
			INNER JOIN terms ON terms.id = termusages.term_id AND terms.term_type LIKE '$_options[term_type]'
			WHERE
				$where
				termusages.termusage_foreign  = '$_options[termusage_foreign]' AND
				termusages.termusage_id      != $_post_id AND
				termusages.term_id IN
				(
					SELECT
						term_id
					FROM
						termusages
					WHERE
						termusages.termusage_foreign = '$_options[termusage_foreign]' AND
						termusages.termusage_id      = $_post_id
				)
			GROUP BY title,url,id
			ORDER BY id DESC
			LIMIT $_options[limit]
		";
		$result = \lib\db::get($query);
		return $result;
	}


	/**
	 * remove tags from post
	 *
	 * @param      <type>  $_post_id  The post identifier
	 */
	public static function remove($_post_id)
	{
		$args =
		[
			'termusage_id'      => $_post_id,
			'termusage_foreign' => 'posts'
		];
		return \lib\db\termusages::remove($args, 'tag');
	}
}
?>