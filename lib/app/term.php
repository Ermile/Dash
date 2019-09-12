<?php
namespace dash\app;

/**
 * Class for user.
 */
class term
{
	public static $sort_field =
	[
		'title',
		'slug',
		'count',
		'type',
		'status',
	];


	public static function lates_term($_args = [])
	{
		if(!isset($_args['limit']))
		{
			$_args['limit'] = 5;
		}

		$_args['order_raw'] = 'terms.id DESC';
		$_args['pagenation'] = false;


		$list = \dash\db\terms::search(null, $_args);



		return $list;
	}

	public static function cat_list($_type = 'cat')
	{
		$check_arg = ['type' => $_type, 'language' => \dash\language::current(), 'status' => 'enable'];

		if(!\dash\option::config('no_subdomain'))
		{
			$subdomain = \dash\url::subdomain();
			if($subdomain)
			{
				$check_arg['subdomain'] = $subdomain;
			}
			else
			{
				$check_arg['subdomain'] = null;
			}
		}

		$result = \dash\db\terms::get($check_arg);
		$temp   = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$temp[] = self::ready($value);
			}
		}
		return $temp;
	}

	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}

		$args = ['id' => $id, 'limit' => 1];

		if(!\dash\option::config('no_subdomain'))
		{
			if(\dash\url::subdomain())
			{
				$args['subdomain'] = \dash\url::subdomain();
			}
			else
			{
				$args['subdomain'] = null;
			}
		}


		$result = \dash\db\terms::get($args);
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

		$title = \dash\app::request('title');
		if(!$title)
		{
			\dash\notif::error(T_("Please set the term title"), 'title');
			return false;
		}

		if(mb_strlen($title) > 150)
		{
			\dash\notif::error(T_("Please set the term title less than 150 character"), 'title');
			return false;
		}


		$slug = \dash\app::request('slug');

		if($slug && mb_strlen($slug) > 150)
		{
			\dash\notif::error(T_("Please set the term slug less than 150 character"), 'slug');
			return false;
		}

		if(!$slug)
		{
			$slug = \dash\utility\filter::slug($title, null, 'persian');
		}
		else
		{
			$slug = \dash\utility\filter::slug($slug, null, 'persian');
		}

		$language = \dash\app::request('language');
		if($language && mb_strlen($language) !== 2)
		{
			\dash\notif::error(T_("Invalid parameter language"), 'language');
			return false;
		}

		if($language && !\dash\language::check($language))
		{
			\dash\notif::error(T_("Invalid parameter language"), 'language');
			return false;
		}

		$desc = \dash\app::request('desc');
		if($desc && mb_strlen($desc) > 500)
		{
			\dash\notif::error(T_("Please set the term desc less than 500 character"), 'desc');
			return false;
		}

		$type = \dash\app::request('type');
		switch ($type)
		{
			case 'tag':
			case 'cat':
			case 'code':
			case 'other':
			case 'help':
			case 'term':
			case 'support_tag':
			case 'help_tag':
				// nothing
				break;

			case 'category':
				$type = 'cat';
				break;

			// check mag is enable
			case 'mag':
			case 'mag_tag':
				if(!\dash\option::config('mag'))
				{
					\dash\notif::error(T_("Invalid term type"), 'type');
					return false;
				}
				break;

			default:
				\dash\notif::error(T_("Invalid term type"), 'type');
				return false;
				break;
		}

		$status = \dash\app::request('status');

		if($status && !in_array($status, ['enable','disable','expired','awaiting','filtered','blocked','spam','violence','pornography','other']))
		{
			\dash\notif::error(T_("Invalid status of term"));
			return false;
		}

		$check_duplicate_args = ['type' => $type, 'slug' => $slug, 'language' => $language, 'limit' => 1];
		if(!\dash\option::config('no_subdomain'))
		{
			$subdomain = \dash\url::subdomain();
			if($subdomain)
			{
				$check_duplicate_args['subdomain'] = $subdomain;
			}
			else
			{
				$check_duplicate_args['subdomain'] = null;
			}
		}

		// check duplicate
		// type+lang+slug
		$check_duplicate = \dash\db\terms::get($check_duplicate_args);

		if(isset($check_duplicate['id']))
		{
			if(intval($check_duplicate['id']) === intval($_id))
			{
				// no problem to edit it
			}
			else
			{
				\dash\notif::error(T_("Duplicate term"), ['type', 'slug', 'language', 'title']);
				return false;
			}
		}

		$excerpt = \dash\app::request('excerpt');
		if($excerpt && mb_strlen($excerpt) > 500)
		{
			\dash\notif::error(T_("Please set the term excerpt less than 500 character"), 'excerpt');
			return false;
		}

		$parent = \dash\app::request('parent');
		if($parent && !\dash\coding::is($parent))
		{
			\dash\notif::error(T_("Invalid parent"), 'parent');
			return false;
		}

		$url = $slug;

		if($type === 'cat')
		{
			if($parent)
			{
				$parent = \dash\coding::decode($parent);

				$get_parent = \dash\db\terms::get(['id' => $parent, 'limit' => 1]);

				if(!isset($get_parent['id']) || !array_key_exists('type', $get_parent) || !array_key_exists('url', $get_parent))
				{
					\dash\notif::error(T_("Parent not found"), 'parent');
					return false;
				}

				if(intval($get_parent['id']) === intval($_id))
				{
					\dash\notif::error(T_("Can not set the parent as yourself"), 'parent');
					return false;
				}
				if($get_parent['type'] != $type)
				{
					\dash\notif::error(T_("The parent is not a :type", ['type' => $type]), 'parent');
					return false;
				}
				$url = $get_parent['url'] . '/'. $slug;
				$url = ltrim($url, '/');
			}
		}

		$meta = [];
		$args = [];

		if(\dash\app::request('color'))
		{
			$meta['color'] = \dash\app::request('color');
		}

		if(\dash\app::request('icon'))
		{
			$meta['icon'] = \dash\app::request('icon');
		}

		if(!empty($meta))
		{
			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
			$args['meta'] = $meta;
		}


		if($url)
		{
			if(!\dash\app\url::check($url))
			{
				return false;
			}
		}

		$subdomain = \dash\url::subdomain();

		$args['title']    = $title;
		$args['parent']   = $parent;
		$args['desc']     = $desc;
		$args['subdomain']= $subdomain;
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
						$result[$key] = \dash\coding::encode($value);
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

				case 'meta':
					if($value && is_string($value))
					{
						$result[$key] = json_decode($value, true);
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
		\dash\app::variable($_args);


		$default_option =
		[
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\dash\user::id())
		{
			if($_option['debug']) \dash\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$return  = [];

		if(!$args['status'])
		{
			$args['status'] = 'enable';
		}

		$term_id = \dash\db\terms::insert($args);

		if(!$term_id)
		{
			\dash\log::set('noWayToAddTerm');
			\dash\notif::error(T_("No way to insert term"));
			return false;
		}

		\dash\log::set('addTerm', ['code' => $term_id, 'datalink' => \dash\coding::encode($term_id)]);

		return $return;
	}

	public static function list($_string = null, $_args = [])
	{

		if(!\dash\user::id())
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

		$result = \dash\db\terms::search($_string, $option, $field);

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
		\dash\app::variable($_args);

		$default_option =
		[

		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$id = \dash\app::request('id');
		$id = \dash\coding::decode($id);

		if(!$id)
		{
			\dash\notif::error(T_("Can not access to edit term"), 'term');
			return false;
		}

		// check args
		$args = self::check($id);

		if(!\dash\app::isset_request('subdomain')) unset($args['subdomain']);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		\dash\db\terms::update($args, $id);

		\dash\log::set('editTerm', ['code' => $id, 'datalink' => \dash\coding::encode($id)]);

		return true;
	}
}
?>