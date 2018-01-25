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
		$myUrl = \lib\router::get_url();
		$myUrl = \lib\router::urlfilterer($myUrl);
		return $myUrl;
	}

	public static function check($_id = null, $_option = [])
	{

		$default_option =
		[
			'meta' => [],
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

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

		$check_duplicate_slug = \lib\db\posts::get(['slug' => $slug, 'language' => $language, 'limit' => 1]);
		if(isset($check_duplicate_slug['id']))
		{
			if(intval($check_duplicate_slug['id']) === intval($_id))
			{
				// no problem to edit it
			}
			else
			{
				\lib\debug::error(T_("Duplicate slug"), 'slug');
				return false;
			}
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

		$type = \lib\app::request('type');
		if($type && mb_strlen($type) > 100)
		{
			\lib\debug::error(T_("Please set the type less than 100 character"), 'type');
			return false;
		}

		if(!$type)
		{
			$type = 'post';
		}


		$comment = \lib\app::request('comment');
		$comment = $comment ? 'open' : 'closed';


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

		if($language === 'fa' && $publishdate)
		{
			$publishdate = \lib\utility\jdate::to_gregorian($publishdate);
		}

		$meta = $_option['meta'];
		if(\lib\app::isset_request('thumb') && \lib\app::request('thumb'))
		{
			$meta['thumb'] = \lib\app::request('thumb');
		}


		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);


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

		$check_all_is_cat = null;

		if($_type === 'tag')
		{
			$tag = $category;
			$tag = explode(',', $tag);
			$tag = array_map(function($_a){return trim($_a);}, $tag);
			$tag = array_filter($tag);
			$tag = array_unique($tag);

			$check_exist_tag = \lib\db\terms::get_mulit_term_title($tag, 'tag');

			$all_tags_id = [];

			$must_insert_tag = $tag;

			if(is_array($check_exist_tag))
			{
				foreach ($check_exist_tag as $key => $value)
				{
					if(isset($value['id']))
					{
						array_push($all_tags_id, intval($value['id']));
					}

					if(isset($value['title']) && in_array($value['title'], $tag))
					{
						unset($tag[array_search($value['title'], $tag)]);
					}
				}
			}

			if(!empty($must_insert_tag))
			{
				$multi_insert_tag = [];
				foreach ($must_insert_tag as $key => $value)
				{
					$slug = \lib\utility\filter::slug($value, null, 'persian');

					$multi_insert_tag[] =
					[
						'type'     => 'tag',
						'status'   => 'enable',
						'title'    => $value,
						'slug'     => $slug,
						'url'      => $slug,
						'user_id'  => \lib\user::id(),
						'language' => \lib\define::get_language(),
					];
				}

				$first_id    = \lib\db\terms::multi_insert($multi_insert_tag);
				$all_tags_id = array_merge($all_tags_id, \lib\db\config::multi_insert_id($multi_insert_tag, $first_id));
			}

			$category_id = $all_tags_id;
		}
		else
		{

			if(!is_array($category) || empty($category) || !$category)
			{
				return null;
			}

			$category_id = array_map(function($_a){return \lib\utility\shortURL::decode($_a);}, $category);
			$category_id = array_filter($category_id);
			$category_id = array_unique($category_id);

			$check_all_is_cat = \lib\db\terms::check_multi_id($category_id, $_type);
			if(count($check_all_is_cat) !== count($category_id))
			{
				\lib\debug::warn(T_("Some :type is wrong", ['type' => T_($_type)]), 'cat');
				return false;
			}

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
			$old_category_id = array_column($get_old_post_cat, 'id');
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


		$new_url = null;

		if($check_all_is_cat)
		{
			$new_url = isset($check_all_is_cat[0]['url']) ? $check_all_is_cat[0]['url'] : null;
		}

		return $new_url;


	}

	public static function find_post()
	{
		$url = self::get_url();

		if(substr($url, 0, 7) == 'static/' || substr($url, 0, 6) == 'files/' || substr($url, 0, 7) == 'static_' || substr($url, 0, 6) == 'files_')
		{
			return false;
		}

		if(file_exists(root. "/content/template/static/$url.html"))
		{
			return false;
		}

		$url = str_replace("'", '', $url);
		$url = str_replace('"', '', $url);
		$url = str_replace('`', '', $url);
		$url = str_replace('%', '', $url);

		$language = \lib\define::get_language();
		$preview  = \lib\utility::get('preview');

		$qry =
		"
			SELECT
				*
			FROM
				posts
			WHERE
				posts.language = '$language' AND
				posts.url      = '$url'
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

				case 'meta':
					if(is_array($value))
					{
						$result['meta'] = $value;
					}
					elseif(is_string($value) && substr($value, 0, 1) === '{')
					{
						$result['meta'] = json_decode($value, true);
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