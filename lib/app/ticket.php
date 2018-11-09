<?php
namespace dash\app;

class ticket
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

	public static $public_show_field =
				"
					comments.*,
					users.avatar,
					users.chatid,
					users.tgusername,
					users.tgstatus,
					users.firstname,
					users.displayname
				 ";
	public static $master_join = "LEFT JOIN users ON users.id = comments.user_id ";


	public static function get_user_in_ticket($_ticket_detail)
	{
		if(!is_array($_ticket_detail))
		{
			return false;
		}

		$ids = array_column($_ticket_detail, 'id');
		if(!is_array($ids) || empty($ids) || !$ids)
		{
			return false;
		}
		$ids = array_unique($ids);
		$ids = array_filter($ids);

		if(empty($ids))
		{
			return false;
		}

		$ids         = implode(',', $ids);
		$user_detail = \dash\db\comments::get_user_in_ticket($ids);

		if(is_array($user_detail))
		{
			$user_detail = array_map(['\dash\app\user', 'ready'], $user_detail);
		}

		$user_detail = array_combine(array_column($user_detail, 'id'), $user_detail);

		foreach ($_ticket_detail as $key => $value)
		{
			if(isset($value['user_in_ticket']) && is_array($value['user_in_ticket']))
			{
				$user_in_ticket_detail = [];

				foreach ($value['user_in_ticket'] as $k => $v)
				{
					if(isset($value['user_id']) && $value['user_id'] === $v)
					{
						continue;
					}
					if(array_key_exists($v, $user_detail))
					{
						$user_in_ticket_detail[] = $user_detail[$v];
					}
				}
				$_ticket_detail[$key]['user_in_ticket_detail'] = $user_in_ticket_detail;
			}
		}

		return $_ticket_detail;
	}

	public static function get($_id)
	{

		if(!$_id || !is_numeric($_id))
		{
			return false;
		}

		$_options['public_show_field'] = self::$public_show_field;

		$_options['master_join'] = self::$master_join;

		$get = \dash\db\comments::get(['comments.id' => $_id, 'limit' => 1], $_options);

		if(is_array($get))
		{
			return self::ready($get);
		}
		return false;
	}


	public static function add($_args)
	{
		$content = null;
		// if(isset($_args['content']))
		// {
		// 	$content = \dash\safe::safe($_args['content'], 'sqlinjection');
		// }

		\dash\app::variable($_args, ['raw_field' => ['content']]);

		// check args
		$args = self::check();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		$args['content']    = \dash\app::request('content');

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

		$dateNow = date("Y-m-d H:i:s");

		$args['visitor_id']  = \dash\utility\visitor::id();
		$args['ip']          = \dash\server::ip(true);
		$args['datecreated'] = $dateNow;


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

		// $replace =
		// [
		// 	'displayname'   => \dash\user::detail('displayname'),
		// 	'link'          => \dash\url::this(). '/show?id='. $comment_id,
		// 	'code'          => $comment_id,
		// 	'ticketContent' => strip_tags($args['content']),
		// 	'ticketTitle'   => isset($args['title']) ? $args['title'] : null,
		// ];

		// \dash\log::set('newTicket', $replace);

		// $notif_args =
		// [
		// 	'send_msg'    =>
		// 	[
		// 		'telegram' => strip_tags($args['content'])
		// 	],
		// ];

		// \dash\notification::send('newTicket', null, $replace, $notif_args);


		$return            = [];
		$return['id']      = $comment_id;
		$return['date']    = $dateNow;
		$return['code']    = md5((string) $comment_id. '^_^-*_*'. $dateNow);
		$return['codeurl'] = \dash\url::kingdom(). '/support/ticket/show?id='. $return['id']. '&guest='. $return['code'];
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

		if(!$_id || !is_numeric($_id))
		{
			\dash\notif::error(T_("Can not access to edit comment"));
			return false;
		}

		$args = self::check($_id);

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

		if(isset($args['status']) && $args['status'] === 'deleted')
		{
			\dash\permission::check('cpCommentsDelete');
		}
		\dash\log::set('editComment', ['data' => $_id, 'datalink' => \dash\coding::encode($_id)]);
		return \dash\db\comments::update($args, $_id);
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

		$result            = \dash\db\comments::search($_string, $_args);
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

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['approved','awaiting','unapproved','spam','deleted','filter','close', 'answered']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}


		$title = \dash\app::request('title');
		if($title && mb_strlen($title) > 400)
		{
			\dash\notif::error(T_("Title is out of range!"));
			return false;
		}

		$file = \dash\app::request('file');
		$parent = \dash\app::request('parent');
		if(\dash\app::isset_request('parent') && \dash\app::request('parent') && !is_numeric($parent))
		{
			\dash\notif::error(T_("Invalid parent"));
			return false;
		}


		$args            = [];
		$args['status']  = $status ? $status : 'awaiting';
		$args['author']  = $author;
		$args['type']    = $type;
		$args['user_id'] = $user_id;

		$args['meta']    = $meta;
		$args['mobile']  = $mobile;
		$args['title']   = $title;
		$args['file']    = $file;
		$args['parent']    = $parent;

		return $args;
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
				case 'status':
					$color       = null;
					$color_class = null;
					switch ($value)
					{
						case 'awaiting':
							$color       = null;
							$color_class = 'pain';
							break;

						case 'unapproved':
							$color       = 'warning';
							$color_class = 'warn';
							break;

						case 'spam':
						case 'deleted':
						case 'filter':
							$color       = 'negative';
							$color_class = 'danger';
							break;

						case 'close':
							$color       = 'disabled';
							$color_class = 'secondary';
							break;

						case 'answered':
							$color       = 'positive';
							$color_class = 'success';
							break;
					}

					if(isset($_data['plus']) && $_data['plus'])
					{
						if($value === 'awaiting')
						{
							$color = 'active';
						}
					}

					$result['rowColor']   = $color;
					$result['colorClass'] = $color_class;
					$result[$key]         = $value;
					break;
				case 'id':
					$result[$key] = $value;
					$datecreated = isset($_data['datecreated']) ? $_data['datecreated'] : null;
					if($datecreated)
					{
						$result['code'] =  md5((string) $value. '^_^-*_*'. $datecreated);
					}
					break;

				case 'user_in_ticket':
					if($value)
					{
						$explode = explode(',', $value);
						$result[$key] = array_map(['\dash\coding', 'encode'], $explode);
					}
					else
					{
						$result[$key] = [];
					}
					break;
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

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}
}
?>