<?php
namespace lib\app;

class posts
{

	use \lib\app\posts\add;
	use \lib\app\posts\datalist;
	use \lib\app\posts\edit;
	use \lib\app\posts\get;

	public static $datarow = null;


	public static function get_url()
	{
		$myUrl = \lib\router::get_url('_');
		$myUrl = \lib\router::urlfilterer($myUrl);
		return $myUrl;
	}

	public static function check()
	{

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

		$title = \lib\app::request('title');
		if(!$title)
		{
			\lib\debug::error(T_("Title of posts can not be null"), 'title');
			return false;
		}

		if($title && mb_strlen($title) > 100)
		{
			\lib\debug::error(T_("Please set the title less than 100 character"), 'title');
			return false;
		}


		$slug = \lib\app::request('slug');
		if($slug && mb_strlen($slug) > 100)
		{
			\lib\debug::error(T_("Please set the slug less than 100 character"), 'slug');
			return false;
		}

		if($title && !$slug)
		{
			$slug = \lib\utility\filter::slug($title, null, 'persian');
		}

		$url = \lib\app::request('url');
		if($url && mb_strlen($url) > 255)
		{
			\lib\debug::error(T_("Please set the url less than 100 character"), 'url');
			return false;
		}

		if(!$url)
		{
			$url = $slug;
		}

		$content = \lib\app::request('content');
		$meta = \lib\app::request('meta');

		$type = \lib\app::request('type');
		if($type && mb_strlen($type) > 100)
		{
			\lib\debug::error(T_("Please set the type less than 100 character"), 'type');
			return false;
		}


		$comment = \lib\app::request('comment');
		if($comment && !in_array($comment, ['open', 'close']))
		{
			\lib\debug::error(T_("Invalid parameter comment"), 'title');
			return false;
		}

		$count = \lib\app::request('count');
		$order = \lib\app::request('order');

		$status = \lib\app::request('status');
		if($status && !in_array($status, ['publish','draft','schedule','deleted','expire']))
		{
			\lib\debug::error(T_("Invalid parameter status"), 'status');
			return false;
		}

		// $parent = \lib\app::request('parent');

		$publishdate = \lib\app::request('publishdate');
		if($publishdate && !\lib\date::db($publishdate))
		{
			\lib\debug::error(T_("Invalid parameter publishdate"), 'publishdate');
			return false;
		}

		$args                = [];
		$args['language']    = $language;
		$args['title']       = $title;
		$args['slug']        = $slug;
		$args['url']         = $url;
		$args['content']     = $content;
		$args['meta']        = $meta;
		$args['type']        = $type;
		$args['comment']     = $comment;
		$args['count']       = $count;
		$args['order']       = $order;
		$args['status']      = $status;
		// $args['parent']      = $parent;
		$args['publishdate'] = $publishdate;

		return $args;

	}


	public static function set_post_term($_post_id, $_type)
	{
		$category = \lib\app::request($_type);

		if(!is_array($category))
		{
			return null;
		}

		$category_id = array_map(function($_a){return \lib\utility\shortURL::decode($_a);}, $category);
		$category_id = array_filter($category_id);
		$category_id = array_unique($category_id);

		$check_all_is_cat = \lib\db\terms::check_multi_id($category_id, $_type);
		if(count($check_all_is_cat) !== count($category_id))
		{
			\lib\debug::warn(T_("Some category is wrong"), 'cat');
			return false;
		}

		$get_old_post_cat = \lib\db\termusages::usage($_post_id, $_type);

		$must_insert = [];
		$must_remove = [];

		if(empty($get_old_post_cat))
		{
			$must_insert = $category_id;
		}
		else
		{
			$old_category_id = array_column($get_old_post_cat, 'term_id');
			$old_category_id = array_map(function($_a){return intval($_a);}, $old_category_id);
			$must_insert = array_diff($category_id, $old_category_id);
			$must_remove = array_diff($old_category_id, $category_id);
		}

		// var_dump($old_category_id, $category_id, $must_insert, $must_remove);exit();

		if(!empty($must_insert))
		{
			$insert_multi = [];
			foreach ($must_insert as $key => $value)
			{
				$insert_multi[] =
				[
					'term_id'    => $value,
					'related_id' => $_post_id,
					'related'    => 'posts',
					'type'       => $_type,
				];
			}
			if(!empty($insert_multi))
			{
				\lib\db\termusages::insert_multi($insert_multi);
			}
		}

		if(!empty($must_remove))
		{
			$must_remove = array_filter($must_remove);
			$must_remove = array_unique($must_remove);

			$must_remove = implode(',', $must_remove);
			\lib\db\termusages::hard_delete(['related_id' => $_post_id, 'related' => 'posts', 'term_id' => ["IN", "($must_remove)"]]);
		}

	}

	public static function find_post()
	{
		$url = self::get_url();
		$url = str_replace("'", '', $url);
		$url = str_replace('"', '', $url);
		$url = str_replace('`', '', $url);
		$url = str_replace('%', '', $url);

		if(substr($url, 0, 7) == 'static/' || substr($url, 0, 6) == 'files/')
		{
			return false;
		}

		$language = \lib\define::get_language();
		$preview  = \lib\utility::get('preview');
		$qry =
		"
			SELECT
				*
			FROM
				posts
			WHERE
			(
				posts.language IS NULL OR
				posts.language = '$language'
			) AND
			posts.url = '$url'
			LIMIT 1
		";

		$datarow = \lib\db::get($qry, null, true);

		if(isset($datarow['user_id']) && (int) $datarow['user_id'] === (int) \lib\user::id())
		{
			// no problem to load this post
		}
		else
		{
			if($preview)
			{
				// no problem to load this post
			}
			else
			{
				if(isset($datarow['status']) && $datarow['status'] == 'publish')
				{
					// no problem to load this poll
				}
				else
				{
					$datarow = false;
				}
			}
		}

		// we have more than one record
		if(isset($datarow[0]))
		{
			$datarow = false;
		}

		if(isset($datarow['id']))
		{
			$id = $datarow['id'];
		}
		else
		{
			$datarow = false;
			$id  = 0;
		}

		self::$datarow = $datarow;

		return $datarow;
	}


	/**
	 * ready data of classroom to load in api
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
				case 'user_id':
				case 'parent':
				case 'term_id':
					if(isset($value))
					{
						$result[$key] = \lib\utility\shortURL::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}
}
?>