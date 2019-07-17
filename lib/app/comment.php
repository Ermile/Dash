<?php
namespace dash\app;

class comment
{

	public static $sort_field =
	[
		'id',
		'plus',
		'minus',
		'datecreated',
		'status',
		'mobile',
		'author',
		'email',
	];


	public static  function get_comment_counter($_args)
	{
		$comment_counter     = \dash\db\comments::get_comment_counter($_args);
		if(is_array($comment_counter))
		{
			$comment_counter['all'] = array_sum($comment_counter);
		}

		return $comment_counter;
	}

	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}
		$get = \dash\db\comments::get(['id' => $id, 'limit' => 1]);
		if(is_array($get))
		{
			return self::ready($get);
		}
		return false;
	}


	public static function add($_args)
	{
		// $content = null;
		// if(isset($_args['content']))
		// {
		// 	$content = \dash\safe::safe($_args['content'], 'sqlinjection');
		// }

		\dash\app::variable($_args);

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		// $args['content']    = $content;

		if(isset($args['user_id']) && is_numeric($args['user_id']))
		{
			$check_duplicate =
			[
				'user_id' => $args['user_id'],
				'content' => $args['content'],
				'limit'   => 1,
			];

			if(isset($args['post_id']) && $args['post_id'])
			{
				$check_duplicate['post_id'] = $args['post_id'];
			}

			if(isset($args['parent']) && $args['parent'])
			{
				$check_duplicate['parent'] = $args['parent'];
			}

			$check_duplicate = \dash\db\comments::get($check_duplicate);

			if(isset($check_duplicate['id']))
			{
				\dash\notif::error(T_("This text is duplicate and you are sended something like this before!"), 'content');
				return false;
			}
		}

		// $args['visitor_id'] = \dash\utility\visitor::id();
		$args['ip']         = \dash\server::ip(true);

		if(\dash\url::subdomain())
		{
			$args['subdomain'] = \dash\url::subdomain();
		}

		$comment_id = \dash\db\comments::insert($args);

		if(!$comment_id)
		{
			\dash\notif::error(T_("No way to add new data"));
			return false;
		}
		\dash\log::set('addComment', ['code' => $comment_id, 'datalink' => \dash\coding::encode($comment_id)]);

		$return       = [];
		$return['id'] = \dash\coding::encode($comment_id);
		return $return;
	}


	public static function edit($_args, $_id)
	{
		$content = null;
		if(isset($_args['content']))
		{
			$content = \dash\safe::safe($_args['content'], 'sqlinjection');
		}

		\dash\app::variable($_args);
		// check args
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			\dash\notif::error(T_("Can not access to edit comment"));
			return false;
		}

		$args = self::check($id);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}
		$args['content'] = $content;

		if(!\dash\app::isset_request('status')) unset($args['status']);
		if(!\dash\app::isset_request('content')) unset($args['content']);
		if(!\dash\app::isset_request('author')) unset($args['author']);
		if(!\dash\app::isset_request('type'))   unset($args['type']);
		if(!\dash\app::isset_request('user_id')) unset($args['user_id']);
		if(!\dash\app::isset_request('post_id')) unset($args['post_id']);
		if(!\dash\app::isset_request('meta'))   unset($args['meta']);
		if(!\dash\app::isset_request('mobile')) unset($args['mobile']);
		if(!\dash\app::isset_request('title')) unset($args['title']);
		if(!\dash\app::isset_request('file')) unset($args['file']);
		if(!\dash\app::isset_request('parent')) unset($args['parent']);
		if(!\dash\app::isset_request('via')) unset($args['via']);
		if(!\dash\app::isset_request('star')) unset($args['star']);
		if(!\dash\app::isset_request('url')) unset($args['url']);
		if(!\dash\app::isset_request('email')) unset($args['email']);


		if(isset($args['status']) && $args['status'] === 'deleted')
		{
			\dash\permission::check('cpCommentsDelete');
		}
		\dash\log::set('editComment', ['code' => $id, 'datalink' => \dash\coding::encode($id)]);
		return \dash\db\comments::update($args, $id);
	}


	public static function list($_string = null, $_args = [])
	{

		$default_meta =
		[
			'pagenation' => true,
			'sort'       => null,
			'order'      => null,
			'join_user'  => false,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_meta, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}

		unset($_args['order']);
		unset($_args['sort']);

		$_args['comments.type'] = 'comment';

		$result            = \dash\db\comments::search_full($_string, $_args);
		$temp              = [];

		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$check = \dash\app::fix_avatar($check);
				$temp[] = $check;
			}
		}

		return $temp;
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

		$content = \dash\app::request('content');

		if(!$content && \dash\app::isset_request('content'))
		{
			\dash\notif::error(T_("Please fill the content box"), 'content');
			return false;
		}

		$author = \dash\app::request('author');
		if($author && mb_strlen($author) >= 100)
		{
			$author = substr($author, 0, 99);
		}

		$type = \dash\app::request('type');
		if($type && mb_strlen($type) >= 50)
		{
			$type = substr($type, 0, 49);
		}

		$meta = \dash\app::request('meta');
		if($meta && (is_array($meta) || is_object($meta)))
		{
			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		}

		$mobile = \dash\app::request('mobile');
		if($mobile && mb_strlen($mobile) > 15)
		{
			$mobile = substr($mobile, 0, 14);
		}

		$user_id = \dash\app::request('user_id');
		if($user_id && !is_numeric($user_id))
		{
			$user_id = null;
		}


		if(!$user_id && \dash\utility\filter::mobile($mobile))
		{
			$get_user = \dash\db\users::get_by_mobile(\dash\utility\filter::mobile($mobile));
			if(isset($get_user['id']))
			{
				$user_id = $get_user['id'];
			}
		}

		$via = \dash\app::request('via');
		if($via && !in_array($via, ['site', 'telegram', 'sms', 'contact', 'admincontact', 'app']))
		{
			\dash\notif::error(T_("Invalid via"), 'via');
			return false;
		}

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['approved','awaiting','unapproved','spam','deleted','filter','close', 'answered']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}

		$star = \dash\app::request('star');
		$star = \dash\utility\convert::to_en_number($star);
		if($star && !is_numeric($star))
		{
			\dash\notif::error(T_("Invalid parameter star, Star must be a number"), 'star');
			return false;
		}

		if($star && !in_array(intval($star), [1,2,3,4,5]))
		{
			\dash\notif::error(T_("Invalid star, Star number must be between 1 and 5"), 'star');
			return false;
		}

		$post_id = \dash\app::request('post_id');
		if($post_id)
		{
			$post_id = \dash\coding::decode($post_id);
			if(!$post_id)
			{
				\dash\notif::error(T_("Invalid post id"));
				return false;
			}

			$check_ok_post = \dash\db\posts::get(['id' => $post_id, 'limit' => 1]);
			if(!isset($check_ok_post['comment']))
			{
				\dash\notif::error(T_("Invalid post id"));
				return false;
			}

			if($check_ok_post['comment'] !== 'open')
			{
				\dash\notif::error(T_("Comment of this post is closed!"));
				return false;
			}
		}
		else
		{
			$post_id = null;
		}

		$title = \dash\app::request('title');
		if($title && mb_strlen($title) > 400)
		{
			\dash\notif::error(T_("Title is out of range!"));
			return false;
		}

		$file = \dash\app::request('file');
		$parent = \dash\app::request('parent');
		$parent = \dash\coding::decode($parent);
		if(\dash\app::isset_request('parent') && \dash\app::request('parent') && !$parent)
		{
			\dash\notif::error(T_("Invalid parent"));
			return false;
		}

		$email = \dash\app::request('email');
		if($email && !\dash\utility\filter::email($email))
		{
			\dash\notif::error(T_("Invalid email address"), 'email');
			return false;
		}

		$url = \dash\app::request('url');
		if($url && !\dash\utility\filter::url($url))
		{
			\dash\notif::error(T_("Invalid url address"), 'url');
			return false;
		}


		$args            = [];
		$args['status']  = $status ? $status : 'awaiting';
		$args['author']  = $author;
		$args['type']    = $type;
		$args['user_id'] = $user_id;
		$args['post_id'] = $post_id;
		$args['meta']    = $meta;
		$args['mobile']  = $mobile;
		$args['title']   = $title;
		$args['file']    = $file;
		$args['parent']  = $parent;
		$args['via']     = $via;
		$args['star']    = $star;
		$args['content'] = $content;
		$args['url']     = $url;
		$args['email']   = $email;

		return $args;
	}


	/**
	 * ready data of classroom to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$_data = \dash\app::fix_avatar($_data);
		$result = [];
		foreach ($_data as $key => $value)
		{

			switch ($key)
			{
				case 'id':
				case 'user_id':
				case 'parent':
				case 'term_id':
				case 'post_id':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
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