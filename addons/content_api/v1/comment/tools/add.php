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


		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \dash\utility::request(),
			]
		];

		// check comment id is exist
		if(!$this->user_id)
		{
			// if($_args['save_log']) \dash\db\logs::set('addon:api:comment:user_id:notfound', $this->user_id, $log_meta);
			// if($_args['debug']) \dash\notif::error(T_("User not found"), 'comment', 'permission');
			// return false;
		}

		/**
		 * check and set the args
		 */
		$return_function = $this->comment_check_args($_args, $args, $log_meta);

		if(!\dash\engine\process::status() || $return_function === false)
		{
			return false;
		}

		// insert new comment team
		if($_args['method'] === 'post')
		{
			\dash\db\comments::insert($args);
			\dash\db::insert_id();
		}
		elseif($_args['method'] === 'patch')
		{
			$id = \dash\utility::request('id');
			$id = \dash\coding::decode($id);
			if(!$id)
			{
				if($_args['save_log']) \dash\db\logs::set('addons:api:comment:id:not:found', $this->user_id, $log_meta);
				if($_args['debug']) \dash\notif::ok(T_("Id not found"));
				return false;
			}

			if(!\dash\utility::isset_request('post_id'))unset($args['post_id']);
			if(!\dash\utility::isset_request('author')) unset($args['author']);
			if(!\dash\utility::isset_request('email'))  unset($args['email']);
			if(!\dash\utility::isset_request('url'))    unset($args['url']);
			if(!\dash\utility::isset_request('content'))unset($args['content']);
			if(!\dash\utility::isset_request('meta'))   unset($args['meta']);
			if(!\dash\utility::isset_request('status')) unset($args['status']);
			if(!\dash\utility::isset_request('parent')) unset($args['parent']);
			if(!\dash\utility::isset_request('user_id'))unset($args['user_id']);
			if(!\dash\utility::isset_request('type'))   unset($args['type']);

			\dash\db\comments::update($args, $id);
		}

		if(\dash\engine\process::status())
		{


			if($_args['method'] === 'post')
			{
				if($_args['save_log']) \dash\db\logs::set('user:send:request', $this->user_id, $log_meta);
				if($_args['debug']) \dash\notif::ok(T_("Thank You For contacting us"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \dash\notif::ok(T_("Comment data updated"));
			}
		}
		else
		{
			if($_args['save_log']) \dash\db\logs::set('user:send:request:fail', $this->user_id, $log_meta);
			if($_args['debug']) \dash\notif::error(T_("We could'nt save the request"));
		}
	}
}
?>