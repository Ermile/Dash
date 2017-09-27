<?php
namespace addons\content_api\v1\parent\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;

trait add
{
	public $parent_id;

	/**
	 * Adds a parent.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_parent($_args = [])
	{
		$default_args =
		[
			'method'      => 'post',
			// first send notify to parent and if the parent accept the request
			// save parent user
			// if this variable is false
			// save the user parent first and not notification was sended
			'send_notify' => true,
			'save_log'    => true,
			'debug'       => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => utility::request(),
			]
		];

		if(!$this->user_id)
		{
			if($_args['save_log']) logs::set('api:parent:user_id:notfound', null, $log_meta);
			if($_args['debug']) debug::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$user_id = utility::request('id');
		$user_id = utility\shortURL::decode($user_id);
		if(!$user_id)
		{
			if($_args['save_log']) logs::set('api:parent:user_id:not:set', null, $log_meta);
			if($_args['debug']) debug::error(T_("User not found"), 'user', 'arguments');
			return false;
		}

		$parent_id = null;

		$mobile = utility::request('mobile');
		if(!$mobile)
		{
			if($_args['save_log']) logs::set('api:parent:mobile:not:set', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Please set the parent mobile"), 'mobile');
			return false;
		}

		$mobile = \lib\utility\filter::mobile($mobile);

		if(!$mobile)
		{
			if($_args['save_log']) logs::set('api:parent:mobile:invalid', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Invalid mobile number"), 'mobile');
			return false;
		}

		$title = utility::request('title');
		if(!$title)
		{
			if($_args['save_log']) logs::set('api:parent:title:not:set', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Please select one title"));
			return false;
		}

		$get_parent_data = \lib\db\users::get_by_mobile($mobile);

		if(!isset($get_parent_data['id']))
		{
			$parent_id = \lib\db\users::signup_quick(['mobile' => $mobile]);
			$get_parent_data['mobile'] = $mobile;
		}
		else
		{
			$parent_id = $get_parent_data['id'];
		}

		$this->parent_id = $parent_id;

		if(intval($parent_id) === intval($user_id))
		{
			if($_args['save_log']) logs::set('api:parent:parent:yourself', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can not set parent yourself"));
			return false;
		}

		$titles =
		[
			'father',
			'mother',
			'sister',
			'brother',
			'grandfather',
			'grandmother',
			'aunt',
			'husband',
			'uncle',
			'boy',
			'girl',
			'spouse',
			'stepmother',
			'stepfather',
			'neighbor',
			'teacher',
			'friend',
			'boss',
			'supervisor',
			'child',
			'grandson',
			'custom',
		];

		if(!in_array($title, $titles))
		{
			if($_args['save_log']) logs::set('api:parent:title:inavalid', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Invalid title"));
			return false;
		}

		$other_title = utility::request('othertitle');
		if($title === 'custom' && !$other_title)
		{
			if($_args['save_log']) logs::set('api:parent:title:othertitle:not:set', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Plase set the other title field"));
			return false;
		}

		if($other_title && mb_strlen($other_title) > 50)
		{
			if($_args['save_log']) logs::set('api:parent:title:othertitle:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set other title less than 50 character"));
			return false;
		}

		if($_args['send_notify'])
		{
			if($this->check_duplicate_request())
			{
				if($_args['save_log']) logs::set('api:parent:title:othertitle:max:lenght', $this->user_id, $log_meta);
				if($_args['debug']) debug::error(T_("Your request was sended to user, wait for answer user"));
				return ;
			}

			$get_user_data = \lib\db\users::get_by_id($user_id);

			$meta                       = [];
			$meta['user_id']            = $user_id;
			$meta['displayname']        = isset($get_user_data['displayname']) 	? $get_user_data['displayname'] 	: null;
			$meta['fileurl']            = isset($get_user_data['fileurl']) 	   	? $get_user_data['fileurl']	 		: null;
			$meta['parent_id']          = isset($get_parent_data['id']) 	   	? $get_parent_data['id'] 			: null;
			$meta['parent_mobile']      = isset($get_parent_data['mobile'])   	? $get_parent_data['mobile'] 		: null;
			$meta['parent_displayname'] = isset($get_parent_data['displayname']) ? $get_parent_data['displayname']  : null;
			$meta['parent_fileurl']     = isset($get_parent_data['fileurl']) 	? $get_parent_data['fileurl'] 		: null;
			$meta['title']              = $title;
			$meta['othertitle']         = $other_title;

			$send_notify =
			[
				'from'            => $user_id,
				'to'              => $parent_id,
				'cat'             => 'set_parent',
				'related_foreign' => 'users',
				'status'		  => 'enable',
				'related_id'      => $user_id,
				'meta'            => json_encode(\lib\utility\safe::safe($meta), JSON_UNESCAPED_UNICODE),
				'needanswer'      => 1,
				'content'         => T_("Are you :title of this user?", ['title' => T_($title)]),
			];

			$set_notify = \lib\db\notifications::set($send_notify);

			if(debug::$status)
			{
				if($_args['debug']) debug::true(T_("Your request was sended"));
			}
			return true;
		}
		else
		{
			// we dont send notification
			$check_exits_parent =
			[
				'user_id'    => $user_id,
				'title'      => $title,
				'othertitle' => $other_title,
				'limit'      => 1,
			];

			$check_exits_parent = \lib\db\userparents::get($check_exits_parent);
			if(isset($check_exits_parent['id']) && isset($check_exits_parent['parent']))
			{
				if(intval($check_exits_parent['parent']) === intval($parent_id))
				{
					// no thing!
				}
				else
				{
					$update = ['parent'     => $parent_id];
					\lib\db\userparents::update($update, $check_exits_parent['id']);
				}
			}
			else
			{
				$insert =
				[
					'user_id'    => $user_id,
					'title'      => $title,
					'othertitle' => $other_title,
					'creator'    => $this->user_id,
					'parent'     => $parent_id,
				];
				\lib\db\userparents::insert($insert);
			}

		}
	}


	/**
	 * { function_description }
	 */
	public function check_duplicate_request()
	{
		$get =
		[
			'user_idsender'   => $this->user_id,
			'user_id'         => $this->parent_id,
			'category'        => 9,
			'status'          => 'enable',
			'related_id'      => $this->user_id,
			'related_foreign' => 'users',
			'needanswer'      => 1,
			'answer'          => null,
			'limit'           => 1,
		];

		$check_notify = \lib\db\notifications::get($get);

		if($check_notify && is_array($check_notify))
		{
			if(isset($check_notify['status']))
			{
				if(array_key_exists('answer', $check_notify) && !$check_notify['answer'])
				{
					return $check_notify;
				}
			}
		}
		return false;

	}

}
?>