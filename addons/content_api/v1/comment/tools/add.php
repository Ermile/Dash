<?php
namespace addons\content_api\v1\comment\tools;


trait add
{

	use comment_check_args;
	/**
	 * Adds a comment.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_comment($_args = [])
	{

		// ready to insert commentteam or commentbranch record
		$args                  = [];

		// default args
		$default_args =
		[
			'method'   => 'post',
			'debug'    => true,
			'save_log' => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		// merge default args and args
		$_args = array_merge($default_args, $_args);

		// set default title of debug
		if($_args['debug']) \lib\notif::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \lib\utility::request(),
			]
		];

		// check comment id is exist
		if(!$this->user_id)
		{
			// if($_args['save_log']) \lib\db\logs::set('addon:api:comment:user_id:notfound', $this->user_id, $log_meta);
			// if($_args['debug']) \lib\notif::error(T_("User not found"), 'comment', 'permission');
			// return false;
		}

		/**
		 * check and set the args
		 */
		$return_function = $this->comment_check_args($_args, $args, $log_meta);

		if(!\lib\notif::$status || $return_function === false)
		{
			return false;
		}

		// insert new comment team
		if($_args['method'] === 'post')
		{
			\lib\db\comments::insert($args);
			\lib\db::insert_id();
		}
		elseif($_args['method'] === 'patch')
		{
			$id = \lib\utility::request('id');
			$id = \lib\utility\shortURL::decode($id);
			if(!$id)
			{
				if($_args['save_log']) \lib\db\logs::set('addons:api:comment:id:not:found', $this->user_id, $log_meta);
				if($_args['debug']) \lib\notif::true(T_("Id not found"));
				return false;
			}

			if(!\lib\utility::isset_request('post_id'))unset($args['post_id']);
			if(!\lib\utility::isset_request('author')) unset($args['author']);
			if(!\lib\utility::isset_request('email'))  unset($args['email']);
			if(!\lib\utility::isset_request('url'))    unset($args['url']);
			if(!\lib\utility::isset_request('content'))unset($args['content']);
			if(!\lib\utility::isset_request('meta'))   unset($args['meta']);
			if(!\lib\utility::isset_request('status')) unset($args['status']);
			if(!\lib\utility::isset_request('parent')) unset($args['parent']);
			if(!\lib\utility::isset_request('user_id'))unset($args['user_id']);
			if(!\lib\utility::isset_request('type'))   unset($args['type']);

			\lib\db\comments::update($args, $id);
		}

		if(\lib\notif::$status)
		{
			if($_args['debug']) \lib\notif::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['save_log']) \lib\db\logs::set('user:send:request', $this->user_id, $log_meta);
				if($_args['debug']) \lib\notif::true(T_("Thank You For contacting us"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \lib\notif::true(T_("Comment data updated"));
			}
		}
		else
		{
			if($_args['save_log']) \lib\db\logs::set('user:send:request:fail', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("We could'nt save the request"));
		}
	}
}
?>