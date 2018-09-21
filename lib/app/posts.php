<?php
namespace dash\app;

class posts
{

	use \dash\app\posts\add;
	use \dash\app\posts\datalist;
	use \dash\app\posts\edit;
	use \dash\app\posts\get;

	public static $datarow = null;


	public static function post_gallery($_post_id, $_file_index, $_type = 'add')
	{
		$post_id = \dash\coding::decode($_post_id);
		if(!$post_id)
		{
			\dash\notif::error(T_("Invalid post id"));
			return false;
		}

		$load_post_meta = \dash\db\posts::get(['id' => $post_id, 'limit' => 1]);

		if(!array_key_exists('meta', $load_post_meta))
		{
			\dash\notif::error(T_("Invalid post id"));
			return false;
		}

		$meta = $load_post_meta['meta'];

		if(is_string($meta) && (substr($meta, 0, 1) === '{' || substr($meta, 0, 1) === '['))
		{
			$meta = json_decode($meta, true);
		}
		elseif(is_array($meta))
		{
			// no thing
		}
		else
		{
			$meta = [];
		}

		if($_type === 'add')
		{
			if(isset($meta['gallery']) && is_array($meta['gallery']))
			{
				if(in_array($_file_index, $meta['gallery']))
				{
					\dash\notif::error(T_("Duplicate file in this gallery"));
					return false;
				}
				array_push($meta['gallery'], $_file_index);
			}
			else
			{
				$meta['gallery'] = [$_file_index];
			}
		}
		else
		{
			if(isset($meta['gallery']) && is_array($meta['gallery']))
			{
				if(!array_key_exists($_file_index, $meta['gallery']))
				{
					\dash\notif::error(T_("Invalid gallery id"));
					return false;
				}
				unset($meta['gallery'][$_file_index]);
			}

		}

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		\dash\log::db('addPostGallery', ['data' => $post_id, 'datalink' => \dash\coding::encode($post_id)]);
		\dash\db\posts::update(['meta' => $meta], $post_id);
		return true;

	}



	public static function get_url()
	{
		$myUrl = \dash\url::directory();
		$myUrl = \dash\url::urlfilterer($myUrl);
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


		if($_id)
		{
			$current_post_detail = \dash\db\posts::get(['id' => $_id, 'limit' => 1]);
			if(isset($current_post_detail['status']))
			{
				if($current_post_detail['status'] === 'publish')
				{
					if(!\dash\permission::check('cpPostsEditPublished'))
					{
						\dash\notif::error(T_("This post is published. And you can not edit it!"));
						return false;
					}
				}
			}

			if(isset($current_post_detail['user_id']))
			{
				if(intval($current_post_detail['user_id']) !== intval(\dash\user::id()))
				{
					if(!\dash\permission::check('cpPostsEditForOthers'))
					{
						\dash\notif::error(T_("This is not your post. And you can not edit it!"));
						return false;
					}
				}
			}

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

		$title = \dash\app::request('title');
		if(!$title)
		{
			\dash\notif::error(T_("Title of posts can not be null"), 'title');
			return false;
		}

		if($title && mb_strlen($title) > 100)
		{
			\dash\notif::error(T_("Please set the title less than 100 character"), 'title');
			return false;
		}

		$excerpt = \dash\app::request('excerpt');
		if($excerpt && mb_strlen($excerpt) > 300)
		{
			\dash\notif::error(T_("Please set the excerpt less than 300 character"), 'excerpt');
			return false;
		}

		$subtitle = \dash\app::request('subtitle');
		if($subtitle && mb_strlen($subtitle) > 300)
		{
			\dash\notif::error(T_("Please set the subtitle less than 300 character"), 'subtitle');
			return false;
		}

		$slug = \dash\app::request('slug');
		if($slug && mb_strlen($slug) > 100)
		{
			\dash\notif::error(T_("Please set the slug less than 100 character"), 'slug');
			return false;
		}

		if($title && !$slug)
		{
			$slug = $title;
		}

		$slug = str_replace(substr($slug, 0, strrpos($slug, '/')). '/', '', $slug);

		$slug = \dash\utility\filter::slug($slug, null, 'persian');

		$check_duplicate_slug = \dash\db\posts::get(['slug' => $slug, 'language' => $language, 'limit' => 1]);
		if(isset($check_duplicate_slug['id']))
		{
			if(intval($check_duplicate_slug['id']) === intval($_id))
			{
				// no problem to edit it
			}
			else
			{
				\dash\notif::error(T_("Duplicate slug"), 'slug');
				return false;
			}
		}

		$url = \dash\app::request('url');
		if($url && mb_strlen($url) > 255)
		{
			\dash\notif::error(T_("Please set the url less than 100 character"), 'url');
			return false;
		}

		if(!$url)
		{
			$url = $slug;
		}

		$content = \dash\app::request('content');
		if(mb_strlen($content) > 1E+5)
		{
			\dash\notif::warn(T_("This text is too large!"), 'content');
		}

		$type = \dash\app::request('type');
		if($type && mb_strlen($type) > 100)
		{
			\dash\notif::error(T_("Please set the type less than 100 character"), 'type');
			return false;
		}

		if(!$type)
		{
			$type = 'post';
		}

		$comment = \dash\app::request('comment');
		$comment = $comment ? 'open' : 'closed';

		$count = \dash\app::request('count');
		$order = \dash\app::request('order');

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['publish','draft','schedule','deleted','expire']))
		{
			\dash\notif::error(T_("Invalid parameter status"), 'status');
			return false;
		}

		if($status === 'deleted')
		{
			if(isset($current_post_detail['type']) && $current_post_detail['type'] === 'page')
			{
				if(!\dash\permission::check('cpPageDelete'))
				{
					\dash\notif::error(T_("You can not delete page"));
					return false;
				}
			}

			if(!\dash\permission::check('cpPostsDelete'))
			{
				\dash\notif::error(T_("You can not delete post"));
				return false;
			}

			if(isset($current_post_detail['user_id']))
			{
				if(intval($current_post_detail['user_id']) !== intval(\dash\user::id()))
				{
					if(!\dash\permission::check('cpPostsDeleteForOthers'))
					{
						\dash\notif::error(T_("This is not your post. And you can not delete it!"));
						return false;
					}
				}
			}
		}

		$parent_url  = null;
		$parent_slug = null;

		$parent = \dash\app::request('parent');

		if($parent)
		{
			$parent = \dash\coding::decode($parent);
			if(!$parent)
			{
				\dash\notif::error(T_("Invalid parameter parent"), 'parent');
				return false;
			}

			$parent_detail = \dash\db\posts::get(['id' => $parent, 'limit' => 1]);
			if(!isset($parent_detail['slug']) || !isset($parent_detail['url']))
			{
				\dash\notif::error(T_("Parent post not found"), 'parent');
				return false;
			}

			if($_id)
			{
				if(intval($parent) === intval($_id))
				{
					\dash\notif::error(T_("Can not set page as parent of self!"), 'parent');
					return false;
				}

				if(isset($current_post_detail['parent']) && intval($current_post_detail['parent']) !== intval($parent))
				{
					$current_post_parent_detail = \dash\db\posts::get(['id' => $current_post_detail['parent'], 'limit' => 1]);

					if(isset($current_post_parent_detail['slug']) && isset($current_post_parent_detail['url']))
					{
						$slug = str_replace($current_post_parent_detail['slug']. '-', '', $slug);
						$url = str_replace($current_post_parent_detail['url']. '/', '', $url);

						$parent_slug = $parent_detail['slug'];
						$parent_url = $parent_detail['url'];
					}
				}
				else
				{
					// no change in slug or url
					$parent_slug = $parent_detail['slug'];
					$parent_url = $parent_detail['url'];
				}

			}
			else
			{
				$parent_slug = $parent_detail['slug'];
				$parent_url = $parent_detail['url'];
			}
		}


		if($parent_slug)
		{
			$slug = $parent_slug . '/'. $slug;
		}

		if($parent_url)
		{
			$url = $parent_url . '/'. $url;
		}

		$publishdate = \dash\app::request('publishdate');
		$publishdate = \dash\utility\convert::to_en_number($publishdate);

		if($publishdate && !\dash\date::db($publishdate))
		{
			\dash\notif::error(T_("Invalid parameter publishdate"), 'publishdate');
			return false;
		}

		if($language === 'fa' && $publishdate)
		{
			$publishdate  = \dash\utility\jdate::to_gregorian($publishdate);
		}

		if(\dash\app::isset_request('publishdate') && !$publishdate)
		{
			$publishdate = date("Y-m-d");
		}

		$publishtime = \dash\app::request('publishtime');
		$publishtime = \dash\utility\convert::to_en_number($publishtime);
		if($publishtime)
		{
			if(\dash\data::make_time($publishtime) === false)
			{
				\dash\notif::error(T_("Invalid publish time"), 'publishtime');
				return false;
			}
			elseif(!$publishtime)
			{
				$publishtime = date("H:i");
			}
		}
		else
		{
			$publishtime = date("H:i");
		}


		$meta = $_option['meta'];
		if(\dash\app::isset_request('thumb') && \dash\app::request('thumb'))
		{
			$meta['thumb'] = \dash\app::request('thumb');
		}

		if(isset($current_post_detail['type']))
		{
			$type = $current_post_detail['type'];
		}

		if(in_array($type, ['post']))
		{
			$cat = \dash\app::request('cat');
			if(!$cat)
			{
				\dash\notif::warn(T_("Category setting for better access is suggested"));
			}
		}



		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);

		if(!$slug)
		{
			\dash\notif::error(T_("Invalid slug"), 'slug');
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
		$args['excerpt']     = $excerpt;
		$args['subtitle']    = $subtitle;
		$args['parent']   = $parent;
		$args['publishdate'] = $publishdate. ' '. $publishtime ;

		return $args;

	}


	public static function set_post_term($_post_id, $_type, $_related = 'posts')
	{
		$have_term_to_save_log = false;

		$category = \dash\app::request($_type);

		$check_all_is_cat = null;

		if(strpos($_type, 'tag') !== false)
		{
			$tag = $category;
			$tag = explode(',', $tag);
			$tag = array_filter($tag);
			$tag = array_unique($tag);

			$check_exist_tag = \dash\db\terms::get_mulit_term_title($tag, $_type);

			$all_tags_id = [];

			$must_insert_tag = $tag;

			if(is_array($check_exist_tag))
			{
				$check_exist_tag = array_column($check_exist_tag, 'title', 'id');
				$check_exist_tag = array_filter($check_exist_tag);
				$check_exist_tag = array_unique($check_exist_tag);

				foreach ($check_exist_tag as $key => $value)
				{

					if(isset($value) && in_array($value, $tag))
					{
						unset($tag[array_search($value, $tag)]);
						unset($must_insert_tag[array_search($value, $must_insert_tag)]);
					}

					array_push($all_tags_id, intval($key));
				}
			}

			$must_insert_tag = array_filter($must_insert_tag);
			$must_insert_tag = array_unique($must_insert_tag);

			if(!empty($must_insert_tag))
			{
				$multi_insert_tag = [];
				foreach ($must_insert_tag as $key => $value)
				{
					$slug = \dash\utility\filter::slug($value, null, 'persian');

					$multi_insert_tag[] =
					[
						'type'     => $_type,
						'status'   => 'enable',
						'title'    => $value,
						'slug'     => $slug,
						'url'      => $slug,
						'user_id'  => \dash\user::id(),
						'language' => \dash\language::current(),
					];
				}
				$have_term_to_save_log = true;
				$first_id    = \dash\db\terms::multi_insert($multi_insert_tag);
				$all_tags_id = array_merge($all_tags_id, \dash\db\config::multi_insert_id($multi_insert_tag, $first_id));
			}

			$category_id = $all_tags_id;
		}
		else
		{
			$category_id = [];

			if($category && is_array($category))
			{
				$category_id = array_map(function($_a){return \dash\coding::decode($_a);}, $category);
				$category_id = array_filter($category_id);
				$category_id = array_unique($category_id);

				$check_all_is_cat = \dash\db\terms::check_multi_id($category_id, $_type);

				if(count($check_all_is_cat) !== count($category_id))
				{
					\dash\notif::warn(T_("Some :type is wrong", ['type' => T_($_type)]), 'cat');
					return false;
				}
			}
		}

		$get_old_post_cat = \dash\db\termusages::usage($_post_id, $_type, $_related);

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

		if(!empty($must_insert))
		{
			$insert_multi = [];
			foreach ($must_insert as $key => $value)
			{
				$insert_multi[] =
				[
					'term_id'    => $value,
					'related_id' => $_post_id,
					'related'    => $_related,
					'type'       => $_type,
				];
			}
			if(!empty($insert_multi))
			{
				$have_term_to_save_log = true;
				\dash\db\termusages::insert_multi($insert_multi);
			}
		}

		if(!empty($must_remove))
		{
			$must_remove = array_filter($must_remove);
			$must_remove = array_unique($must_remove);

			$must_remove = implode(',', $must_remove);

			\dash\log::db('removePostTerm', ['data' => $_type, 'datalink' => \dash\coding::encode($_post_id)]);
			\dash\db\termusages::hard_delete(['related_id' => $_post_id, 'related' => $_related, 'term_id' => ["IN", "($must_remove)"]]);
		}


		$new_url = null;

		if($check_all_is_cat)
		{
			$new_url = isset($check_all_is_cat[0]['url']) ? $check_all_is_cat[0]['url'] : null;
		}

		if($have_term_to_save_log)
		{
			\dash\log::db('setPostTerm', ['data' => $_type, 'datalink' => \dash\coding::encode($_post_id)]);
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

		$language = \dash\language::current();
		$preview  = \dash\request::get('preview');

		$datarow = \dash\db\posts::get(['language' => $language, 'url' => $url, 'limit' => 1]);

		if(isset($datarow['user_id']) && (int) $datarow['user_id'] === (int) \dash\user::id())
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

		if(is_array($datarow))
		{
			$datarow = self::ready($datarow);
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
						$result[$key] = \dash\coding::encode($value);
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

				case 'slug':
					$result[$key] = $value;
					$split = explode('/', $value);
					$parent_url = [];
					$my_parent_url = [];
					if(count($split) > 1)
					{
						foreach ($split as $index => $parent_slug)
						{
							$parent_url[] = $parent_slug;
							$my_parent_url[] = implode('/', $parent_url);
						}

						array_pop($my_parent_url);
					}

					$result['parent_url'] = $my_parent_url;

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