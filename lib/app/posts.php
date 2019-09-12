<?php
namespace dash\app;

class posts
{
	private static $raw_field = ['content'];
	public static $datarow = null;

	use \dash\app\posts\add;
	use \dash\app\posts\datalist;
	use \dash\app\posts\edit;
	use \dash\app\posts\get;


	public static function category_count()
	{
		$lang = \dash\language::current();
		$list = \dash\db\terms::category_count($lang);

		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				$list[$key]['link'] = \dash\url::kingdom(). '/category/'. $value['url'];
			}
		}
		return $list;
	}

	public static function all_post_title($_args)
	{
		$list = \dash\db\posts::all_post_title($_args);
		if(is_array($list))
		{
			$list = array_map(['self', 'ready'], $list);
		}

		return $list;
	}


	public static function get_user_can_write_post($_type)
	{
		switch ($_type)
		{
			case 'page':
				$permission = 'cpPageAdd';
				break;

			case 'help':
				$permission = 'cpHelpCenterAdd';
				break;

			case 'post':
			default:
				$permission = 'cpPostsAdd';
				break;
		}

		$who_have = \dash\permission::who_have($permission);
		if(\dash\permission::supervisor())
		{
			$who_have[] = 'supervisor';
		}

		$load_user = \dash\db\users::get_by_permission($who_have);
		if(is_array($load_user))
		{
			$load_user = array_map(['\\dash\\app\\user', 'ready'], $load_user);
		}

		return $load_user;
	}


	public static  function get_post_counter($_args)
	{
		$post_counter     = \dash\db\posts::get_post_counter($_args);
		if(is_array($post_counter))
		{
			$post_counter['all'] = array_sum($post_counter);
		}

		return $post_counter;
	}


	public static function lates_post($_args = [])
	{
		if(!isset($_args['limit']))
		{
			$_args['limit'] = 5;
			$_args['end_limit'] = 5;
		}

		$_args['order_raw'] = 'posts.id DESC';
		$_args['pagenation'] = false;
		$_args['status'] = 'publish';

		$list = \dash\db\posts::search(null, $_args);

		if(is_array($list))
		{
			$list = array_map(['\\dash\\app\\posts', 'ready'], $list);
		}

		return $list;
	}


	public static function all_word_cloud($_args = [])
	{
		$word             = [];
		$myCount          = [];

		$allPost          = \dash\db\posts::get_words_chart($_args);

		$countAllPost     = count($allPost);

		if($countAllPost < 10)
		{
			$maxCountWord = 2;
		}
		elseif($countAllPost < 200)
		{
			$maxCountWord = 10;
		}
		else
		{
			$maxCountWord = 50;
		}

		if(is_array($allPost))
		{
			foreach ($allPost as $key => $value)
			{
				if(is_array($value))
				{
					foreach ($value as $k => $v)
					{
						$temp      = self::remove_2_char($v);
						$word[]    = $temp;
						$myCountTemp = array_count_values(explode(' ', $temp));
						foreach ($myCountTemp as $myWord => $myCountWord)
						{
							if($myCountWord > $maxCountWord)
							{
								if(isset($myCount[$myWord]))
								{
									$myCount[$myWord] = $myCount[$myWord] + $myCountWord;
								}
								else
								{
									$myCount[$myWord] = $myCountWord;
								}
							}
						}
					}
				}
			}
		}

		$result = [];
		foreach ($myCount as $key => $value)
		{
			$result[] = ['name' => $key, 'weight' => intval($value)];
		}

		$result = json_encode($result, JSON_UNESCAPED_UNICODE);

		return $result;
	}


	private static function remove_2_char($_text)
	{
		$word = [];
		$_text = strip_tags($_text);
		$_text = str_replace('[', ' ', $_text);
		$_text = str_replace(']', ' ', $_text);
		$_text = str_replace('{', ' ', $_text);
		$_text = str_replace('}', ' ', $_text);
		$_text = str_replace('"', ' ', $_text);
		$_text = str_replace('؛', ' ', $_text);
		$_text = str_replace("'", ' ', $_text);
		$_text = str_replace('(', ' ', $_text);
		$_text = str_replace(')', ' ', $_text);
		$_text = str_replace(':', ' ', $_text);
		$_text = str_replace(',', ' ', $_text);
		$_text = str_replace('،', ' ', $_text);
		$_text = str_replace('-', ' ', $_text);
		$_text = str_replace('_', ' ', $_text);
		$_text = str_replace('?', ' ', $_text);
		$_text = str_replace('؟', ' ', $_text);
		$_text = str_replace('.', ' ', $_text);
		$_text = str_replace('=', ' ', $_text);
		$_text = str_replace('
', ' ', $_text);

		$_text = str_replace("\n", ' ', $_text);
		$_text = str_replace('!', ' ', $_text);
		$_text = str_replace('&nbsp;', ' ', $_text);

		$split = explode(" ", $_text);

		foreach ($split as $key => $value)
		{
			$value = trim($value);
			if(mb_strlen($value) > 2 && !is_numeric($value))
			{
				$word[] = $value;
			}
		}

		$word = implode(' ', $word);
		$word = trim($word);
		return $word;
	}
	public static function home_chart($_args)
	{

		$home_chart = \dash\db\posts::home_chart();

		if(!is_array($home_chart))
		{
			$home_chart = [];
		}

		$categories = [];
		$values     = [];
		$hi_chart   = [];

		foreach ($home_chart as $key => $value)
		{
			if(array_key_exists('type', $value))
			{
				$categories[] = T_(ucfirst($value['type']));
			}

			if(array_key_exists('count', $value))
			{
				$values[] = intval($value['count']);
			}
		}

		$hi_chart['categories'] = json_encode($categories, JSON_UNESCAPED_UNICODE);
		$hi_chart['value']      = json_encode($values, JSON_UNESCAPED_UNICODE);

		return $hi_chart;

	}


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
		\dash\log::set('addPostGallery', ['code' => $post_id, 'datalink' => \dash\coding::encode($post_id)]);
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
			'meta'     => [],
			'raw_args' => [],
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$current_post_detail            = [];
		$current_post_detail['user_id'] = null;
		$current_post_detail['type']    = null;
		$current_post_detail['status']  = null;

		if($_id)
		{

			$current_post_detail = \dash\db\posts::get(['id' => $_id, 'limit' => 1]);
			if(!isset($current_post_detail['user_id']) || !isset($current_post_detail['status']) || !isset($current_post_detail['type']))
			{
				\dash\notif::error(T_("Invalid id"));
				return false;
			}

			if($current_post_detail['status'] === 'publish')
			{
				switch ($current_post_detail['type'])
				{
					case 'help':
						if(!\dash\permission::check('cpHelpCenterEditPublished'))
						{
							\dash\notif::error(T_("This help center is published. And you can not edit it!"));
							return false;
						}
						break;

					case 'post':
					case 'page':
					default:
						if(!\dash\permission::check('cpPostsEditPublished'))
						{
							\dash\notif::error(T_("This post is published. And you can not edit it!"));
							return false;
						}

						break;
				}
			}


			if(intval($current_post_detail['user_id']) !== intval(\dash\user::id()))
			{
				switch ($current_post_detail['type'])
				{
					case 'help':
						if(!\dash\permission::check('cpHelpCenterEditForOthers'))
						{
							\dash\notif::error(T_("This is not your post. And you can not edit it!"));
							return false;
						}
						break;

					case 'post':
					case 'page':
					default:
						if(!\dash\permission::check('cpPostsEditForOthers'))
						{
							\dash\notif::error(T_("This is not your post. And you can not edit it!"));
							return false;
						}
						break;
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

		$seotitle = \dash\app::request('seotitle');
		if($seotitle && mb_strlen($seotitle) > 100)
		{
			\dash\notif::error(T_("Please set the seotitle less than 100 character"), 'seotitle');
			return false;
		}

		// if(!$seotitle)
		// {
		// 	$seotitle = $title . ' | '. T_(\dash\option::config('site', 'title'));
		// }

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


		$check_duplicate_args = ['slug' => $slug, 'language' => $language, 'limit' => 1];

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

		$check_duplicate_slug = \dash\db\posts::get($check_duplicate_args);
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



		$special = \dash\app::request('special');

		if($special && !\dash\app\posts\special::check($special))
		{
			\dash\notif::error(T_("Invalid parameter special"), 'special');
			return false;
		}



		$status = \dash\app::request('status');
		if($status && !in_array($status, ['publish','draft','schedule','deleted','expire']))
		{
			\dash\notif::error(T_("Invalid parameter status"), 'status');
			return false;
		}

		if($status === 'deleted')
		{
			switch ($current_post_detail['type'])
			{
				case 'help':
					if(!\dash\permission::check('cpHelpCenterDelete'))
					{
						\dash\notif::error(T_("You can not delete help center"));
						return false;
					}
					break;

				case 'page':
					if(!\dash\permission::check('cpPageDelete'))
					{
						\dash\notif::error(T_("You can not delete page"));
						return false;
					}


				case 'post':
				default:
					if(!\dash\permission::check('cpPostsDelete'))
					{
						\dash\notif::error(T_("You can not delete post"));
						return false;
					}
					break;
			}

			if(intval($current_post_detail['user_id']) !== intval(\dash\user::id()))
			{
				switch ($current_post_detail['type'])
				{
					case 'help':
						if(!\dash\permission::check('cpHelpCenterDeleteForOthers'))
						{
							\dash\notif::error(T_("This is not your help center. And you can not delete it!"));
							return false;
						}
						break;

					case 'post':
					case 'page':
					default:
						if(!\dash\permission::check('cpPostsDeleteForOthers'))
						{
							\dash\notif::error(T_("This is not your post. And you can not delete it!"));
							return false;
						}
						break;
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

		if(\dash\app::isset_request('publishdate') && !$publishdate && $status === 'publish')
		{
			$publishdate = date("Y-m-d");
		}

		$publishtime = \dash\app::request('publishtime');
		$publishtime = \dash\utility\convert::to_en_number($publishtime);
		if($publishtime)
		{
			$publishtime = \dash\date::make_time($publishtime);
			if($publishtime === false)
			{
				\dash\notif::error(T_("Invalid publish time"), 'publishtime');
				return false;
			}
			elseif(!$publishtime)
			{
				$publishtime = date("H:i");
			}
		}
		elseif($status === 'publish')
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

		if(in_array($type, ['post', 'mag']))
		{
			$cat = \dash\app::request('cat');
			if(!$cat && \dash\app::isset_request('cat'))
			{
				\dash\notif::warn(T_("Category setting for better access is suggested"));
			}
		}

		$icon = \dash\app::request('icon');
		if($icon)
		{
			$meta['icon'] = $icon;
		}


		$btntitle = \dash\app::request('btntitle');
		if($btntitle && mb_strlen($btntitle) > 100)
		{
			\dash\notif::error(T_("Button title is out of range"), 'btntitle');
			return false;
		}


		$btnurl = \dash\app::request('btnurl');
		if($btnurl && mb_strlen($btnurl) > 1000)
		{
			\dash\notif::error(T_("Button url is out of range"), 'btnurl');
			return false;
		}

		if($btnurl && isset($_option['raw_args']['btnurl']))
		{
			$btnurl = \dash\safe::safe($_option['raw_args']['btnurl'], 'get_url');
		}

		$btntarget = \dash\app::request('btntarget') ? true : false;

		$btncolor = \dash\app::request('btncolor');
		if($btncolor && !in_array($btncolor, ['primary','primary2','secondary','secondary2','success','success2','danger','danger2','warning','warning2','info','info2','light','dark','pain']))
		{
			\dash\notif::error(T_("Invalid button color"), 'btncolor');
			return false;
		}


		$srctitle = \dash\app::request('srctitle');
		if($srctitle && mb_strlen($srctitle) > 200)
		{
			\dash\notif::error(T_("Sourse title is out of range"), 'srctitle');
			return false;
		}


		$srcurl = \dash\app::request('srcurl');
		if($srcurl && mb_strlen($srcurl) > 1000)
		{
			\dash\notif::error(T_("Sourse url is out of range"), 'srcurl');
			return false;
		}


		if($srcurl && isset($_option['raw_args']['srcurl']))
		{
			$srcurl = \dash\safe::safe($_option['raw_args']['srcurl'], 'get_url');
		}


		$redirecturl = \dash\app::request('redirecturl');
		if($redirecturl && mb_strlen($redirecturl) > 1000)
		{
			\dash\notif::error(T_("Sourse url is out of range"), 'redirecturl');
			return false;
		}


		if($redirecturl && isset($_option['raw_args']['redirecturl']))
		{
			$redirecturl = \dash\safe::safe($_option['raw_args']['redirecturl'], 'get_url');
		}

		$meta['download'] =
		[
			'title'  => $btntitle,
			'url'    => $btnurl,
			'target' => $btntarget,
			'color'  => $btncolor,
		];

		$meta['source'] =
		[
			'title' => $srctitle,
			'url'   => $srcurl,
		];

		$meta['redirect'] = $redirecturl;



		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);

		if(!$slug)
		{
			\dash\notif::error(T_("Invalid slug"), 'slug');
			return false;
		}

		$allow_post_type   = ['post', 'page', 'help', 'mag', 'attachment'];
		$config_allow_post = \dash\option::config('allow_post_type');

		if($config_allow_post && is_array($config_allow_post))
		{
			$allow_post_type = array_merge($config_allow_post, $allow_post_type);
		}

		if($type && !in_array($type, $allow_post_type))
		{
			\dash\notif::error(T_("Invalid type"));
			return false;
		}

		$args                = [];

		if(\dash\permission::check('cpChangePostCreator'))
		{
			$creator = \dash\app::request('creator');

			if($creator && isset($current_post_detail['type']) && isset($current_post_detail['user_id']))
			{
				if(intval(\dash\coding::decode($creator)) !== intval($current_post_detail['user_id']))
				{
					$can_change = self::get_user_can_write_post($current_post_detail['type']);
					if(is_array($can_change))
					{
						$can_change = array_column($can_change, 'id');
						if(!in_array($creator, $can_change))
						{
							\dash\notif::error(T_("Invalid user"));
							return false;
						}
						$args['user_id'] = \dash\coding::decode($creator);
					}
				}
			}
		}


		$subtype = \dash\app::request('subtype');
		if($subtype && !in_array($subtype, ['standard','image', 'gallery', 'video', 'audio']))
		{
			\dash\notif::error(T_("Invalid type"), 'subtype');
			return false;
		}

		if(!$subtype)
		{
			$subtype = 'standard';
		}


		if($url)
		{
			if(!\dash\app\url::check($url))
			{
				return false;
			}
		}


		$args['language']    = $language;
		$args['subtype']    = $subtype;
		$args['subdomain']   = \dash\app::request('subdomain');
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
		$args['parent']      = $parent;
		$args['special']     = $special;
		$args['seotitle']    = $seotitle;
		$args['publishdate'] = $publishdate ? $publishdate. ' '. $publishtime  : null;

		// check status
		switch ($current_post_detail['type'])
		{
			case 'help':
				if(!\dash\permission::check('cpHelpCenterEditStatus'))
				{
					unset($args['status']);
				}
				break;

			default:
				if(!\dash\permission::check('cpPostsEditStatus'))
				{
					unset($args['status']);
				}
				break;
		}

		return $args;

	}


	public static function set_post_term($_post_id, $_type, $_related = 'posts', $_data = [])
	{
		$have_term_to_save_log = false;

		// set default
		if($_related === null)
		{
			$_related = 'posts';
		}

		$check_subdomain = false;

		if(!\dash\option::config('no_subdomain'))
		{
			$check_subdomain = \dash\url::subdomain();
		}

		$category = \dash\app::request($_type);
		if(!$category && $_data)
		{
			$category = $_data;
		}

		$check_all_is_cat = null;

		if(strpos($_type, 'tag') !== false)
		{
			$tag = $category;
			$tag = explode(',', $tag);
			$tag = array_filter($tag);
			$tag = array_unique($tag);

			$check_exist_tag = \dash\db\terms::get_mulit_term_title($tag, $_type, $check_subdomain);

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
						'type'      => $_type,
						'status'    => 'enable',
						'title'     => $value,
						'slug'      => $slug,
						'url'       => $slug,
						'user_id'   => \dash\user::id(),
						'language'  => \dash\language::current(),
						'subdomain' => \dash\url::subdomain(),
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

				$check_all_is_cat = \dash\db\terms::check_multi_id($category_id, $_type, $check_subdomain);

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

			\dash\log::set('removePostTerm', ['code' => $_type, 'datalink' => \dash\coding::encode($_post_id)]);
			\dash\db\termusages::hard_delete(['related_id' => $_post_id, 'related' => $_related, 'term_id' => ["IN", "($must_remove)"]]);
		}


		$new_url = null;

		if($check_all_is_cat)
		{
			$new_url = isset($check_all_is_cat[0]['url']) ? $check_all_is_cat[0]['url'] : null;
		}

		if($have_term_to_save_log)
		{
			\dash\log::set('setPostTerm', ['code' => $_type, 'datalink' => \dash\coding::encode($_post_id)]);
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

		if(file_exists(\dash\engine\content::get_addr(). "template/static/$url.html"))
		{
			return false;
		}

		$url = str_replace("'", '', $url);
		$url = str_replace('"', '', $url);
		$url = str_replace('`', '', $url);
		$url = str_replace('%', '', $url);

		$language = \dash\language::current();
		$preview  = \dash\request::get('preview');

		// load attachments
		// if(substr($url, 0, 6) === 'image/' || substr($url, 0, 6) === 'video/' )
		// {
		// 	$datarow = \dash\db\posts::get(['url' => $url, 'limit' => 1]);
		// }
		// else
		// {
			$datarow = \dash\db\posts::get(['language' => $language, 'url' => $url, 'limit' => 1]);
		// }

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
				case 'parent':
					if(isset($value))
					{
						$parent_detail  = \dash\db\posts::get(['id' => $value, 'limit' => 1]);

						$myParentDetail = [];

						if(isset($parent_detail['title']))
						{
							$myParentDetail['title'] = $parent_detail['title'];
						}

						if(isset($parent_detail['slug']))
						{
							$myParentDetail['slug'] = $parent_detail['slug'];
						}

						if(isset($parent_detail['url']))
						{
							$myParentDetail['url'] = $parent_detail['url'];
						}

						if(!empty($myParentDetail))
						{
							$result['parent_detail'] = $myParentDetail;
						}

						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}

					break;
				case 'id':
				case 'user_id':
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
					$result['meta'] = null;
					if(is_array($value))
					{
						$result['meta'] = $value;
					}
					elseif(is_string($value))
					{
						$result['meta'] = json_decode($value, true);
						if(!is_array($result['meta']))
						{
							$result['meta'] = [];
						}
					}

					$result['file'] = self::makeFileAddr($result['meta']);

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

					$result['slug_raw']   = end($split);
					$result['parent_url'] = $my_parent_url;
					break;

				case 'url':
					$result[$key] = $value;
					$result['link'] = \dash\url::kingdom(). '/'. $value;
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}

	private static function makeFileAddr($_meta)
	{
		if(!isset($_meta['thumb']))
		{
			return null;
		}

		$thumb = $_meta['thumb'];
		if(!$thumb)
		{
			return null;
		}

		$file_addr = substr($thumb, 0, strrpos($thumb, '.'));
		$ext       = str_replace($file_addr, '', $thumb);
		$files =
		[
			'main'   => $thumb,
			'large'  => $file_addr. '-large'. $ext,
			'normal' => $file_addr. '-normal'. $ext,
			'thumb'  => $file_addr. '-thumb'. $ext,
		];
		return $files;
	}


	public static function remove_thumb($_id)
	{
		$detail = \dash\app\posts::get($_id);
		if(!$detail)
		{
			\dash\notif::error(T_("Post not found"));
			return false;
		}

		if(isset($detail['meta']))
		{
			$meta = $detail['meta'];
		}
		else
		{
			$meta = null;
		}


		if(is_array($meta))
		{
			unset($meta['thumb']);
			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		}
		else
		{
			$meta = null;
		}

		\dash\db\posts::update(['meta' => $meta], \dash\coding::decode($_id));
		\dash\notif::ok(T_("The Featured Image removed"));
		return true;

	}
}
?>