<?php
namespace lib\app;

/**
 * Class for user.
 */
class term
{
	public static $sort_field =
	[
		'title',
		'slug',
	];

	public static function get($_id)
	{
		$id = \lib\utility\shortURL::decode($_id);
		if(!$id)
		{
			return false;
		}

		$result = \lib\db\terms::get(['id' => $id, 'limit' => 1]);
		$temp = [];
		if(is_array($result))
		{
			$temp = self::ready($result);
		}
		return $temp;
	}
	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function check($_id = null)
	{

		$title = \lib\app::request('title');
		if(!$title)
		{
			\lib\debug::error(T_("Please set the term title"), 'title');
			return false;
		}

		if(mb_strlen($title) > 150)
		{
			\lib\debug::error(T_("Please set the term title less than 150 character"), 'title');
			return false;
		}


		$slug = \lib\app::request('slug');

		if($slug && mb_strlen($slug) > 150)
		{
			\lib\debug::error(T_("Please set the term slug less than 150 character"), 'slug');
			return false;
		}

		if(!$slug)
		{
			$slug = \lib\utility\filter::slug($title, null, 'persian');
		}
		else
		{
			$slug = \lib\utility\filter::slug($slug, null, 'persian');
		}

		$language = \lib\app::request('language');
		if($language && mb_strlen($language) !== 2)
		{
			\lib\debug::error(T_("Invalid parameter language"), 'language');
			return false;
		}

		if($language && !\lib\utility\location\languages::check($language))
		{
			\lib\debug::error(T_("Invalid parameter language"), 'language');
			return false;
		}

		$desc = \lib\app::request('desc');
		if($desc && mb_strlen($desc) > 500)
		{
			\lib\debug::error(T_("Please set the term desc less than 500 character"), 'desc');
			return false;
		}

		$type = \lib\app::request('type');
		switch ($type)
		{
			case 'tag':
			case 'cat':
			case 'code':
			case 'other':
			case 'term':
				// nothing
				break;

			case 'category':
				$type = 'cat';
				break;

			default:
				\lib\debug::error(T_("Please set the term type"), 'type');
				return false;
				break;
		}

		$status = \lib\app::request('status');

		if($status && !in_array($status, ['enable', 'disable']))
		{
			\lib\debug::error(T_("Invalid status of term"));
			return false;
		}

		// check duplicate
		// type+lang+slug
		$check_duplicate = \lib\db\terms::get(['type' => $type, 'slug' => $slug, 'language' => $language, 'limit' => 1]);

		if(isset($check_duplicate['id']))
		{
			if(intval($check_duplicate['id']) === intval($_id))
			{
				// no problem to edit it
			}
			else
			{
				\lib\debug::error(T_("Duplicate term"), ['type', 'slug', 'language', 'title']);
				return false;
			}
		}

		$excerpt = \lib\app::request('excerpt');
		if($excerpt && mb_strlen($excerpt) > 500)
		{
			\lib\debug::error(T_("Please set the term excerpt less than 500 character"), 'excerpt');
			return false;
		}

		$parent = \lib\app::request('parent');
		if($parent && !\lib\utility\shortURL::is($parent))
		{
			\lib\debug::error(T_("Invalid parent"), 'parent');
			return false;
		}

		$url = $slug;

		if($type === 'cat')
		{
			if($parent)
			{
				$parent = \lib\utility\shortURL::decode($parent);

				$get_parent = \lib\db\terms::get(['id' => $parent, 'limit' => 1]);

				if(!isset($get_parent['id']) || !array_key_exists('type', $get_parent) || !array_key_exists('url', $get_parent))
				{
					\lib\debug::error(T_("Parent not found"), 'parent');
					return false;
				}

				if(intval($get_parent['id']) === intval($_id))
				{
					\lib\debug::error(T_("Can not set the parent as yourself"), 'parent');
					return false;
				}
				if($get_parent['type'] != $type)
				{
					\lib\debug::error(T_("The parent is not a :type", ['type' => $type]), 'parent');
					return false;
				}
				$url = $get_parent['url'] . '/'. $slug;
				$url = ltrim($url, '/');
			}
		}

		$args             = [];
		$args['title']    = $title;
		$args['parent']   = $parent;
		$args['desc']     = $desc;
		$args['status']   = $status;
		$args['slug']     = $slug;
		$args['url']      = $url;
		$args['type']     = $type;
		$args['language'] = $language;
		$args['excerpt']  = $excerpt;

		return $args;
	}


	/**
	 * ready data of user to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'parent':
					if(isset($value))
					{
						$result[$key] = \lib\utility\shortURL::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				case 'type':
					if($value === 'cat')
					{
						$result[$key] = 'category';
					}
					else
					{
						$result[$key] = $value;
					}
					break;

				case 'url':
					if(isset($_data['type']))
					{
						if($_data['type'] === 'cat')
						{
							$result[$key] = 'category/'. $value;
						}
						else
						{
							$result[$key] = $_data['type'] . '/'. $value;
						}
					}
					else
					{
						$result[$key] = $value;
					}
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}


	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{
		\lib\app::variable($_args);


		$default_option =
		[
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\lib\user::id())
		{
			if($_option['debug']) \lib\debug::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		$return         = [];

		$term_id = \lib\db\terms::insert($args);

		if(!$term_id)
		{
			\lib\debug::error(T_("No way to insert term"));
			return false;
		}

		return $return;
	}

	public static function list($_string = null, $_args = [])
	{

		if(!\lib\user::id())
		{
			return false;
		}

		$default_args =
		[
			'order' => null,
			'sort'  => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$option = [];
		$option = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				unset($option['order']);
			}
		}

		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		unset($option['in']);

		$result = \lib\db\terms::search($_string, $option, $field);

		$temp            = [];


		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}


	public static function edit($_args, $_option = [])
	{
		\lib\app::variable($_args);

		$default_option =
		[

		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$id = \lib\app::request('id');
		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			\lib\debug::error(T_("Can not access to edit term"), 'term');
			return false;
		}

		// check args
		$args = self::check($id);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		\lib\db\terms::update($args, $id);

		return true;
	}
}
?>