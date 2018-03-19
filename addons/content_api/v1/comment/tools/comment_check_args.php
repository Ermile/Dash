<?php
namespace addons\content_api\v1\comment\tools;


trait comment_check_args
{
	public function comment_check_args($_args, &$args, $_log_meta, $_type = 'insert')
	{
		$log_meta = $_log_meta;

		$post_id = \lib\utility::request('post_id');
		$post_id = \lib\coding::decode($post_id);
		if(!$post_id)
		{
			$post_id = null;
		}

		$author  = \lib\utility::request('author');
		if($author && mb_strlen($author) >= 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:author:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the author less than 50 character"), 'author', 'arguments');
			return false;
		}

		$email   = \lib\utility::request('email');
		if($email && mb_strlen($email) >= 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:email:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the email less than 50 character"), 'email', 'arguments');
			return false;
		}

		$url     = \lib\utility::request('url');
		if($url && mb_strlen($url) >= 1500)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:url:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the url less than 1500 character"), 'url', 'arguments');
			return false;
		}

		$content = \lib\utility::request('content');
		if($content && mb_strlen($content) >= 5000)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:content:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Your text is too large!"), 'content', 'arguments');
			return false;
		}

		if(!$content && $_args['method'] === 'post')
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:content:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Content can not be null"), 'content', 'arguments');
			return false;
		}

		$meta    = \lib\utility::request('meta');

		if(is_array($meta) || is_object($meta))
		{
			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		}

		if(!is_string($meta) && !is_numeric($meta))
		{
			$meta = null;
		}

		$status  = \lib\utility::request('status');
		if($status && !in_array($status, ['approved','unapproved','spam','deleted']))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:status:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid parameter status"), 'status', 'arguments');
			return false;
		}

		$parent = \lib\utility::request('parent');
		$parent = \lib\coding::decode($parent);
		if(!$parent)
		{
			$parent = null;
		}

		$user_id = \lib\utility::request('user_id');
		$user_id = \lib\coding::decode($user_id);
		if(!$user_id && \lib\utility::request('user_id'))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:user_id:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid parameter user_id"), 'user_id', 'arguments');
			return false;
		}

		$type    = \lib\utility::request('type');//'comment','rate'
		if($type && mb_strlen($type) >= 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:comment:type:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the type less than 50 character"), 'type', 'arguments');
			return false;
		}


		$args['post_id'] = $post_id;
		$args['author']  = trim($author);
		$args['email']   = trim($email);
		$args['url']     = trim($url);
		$args['content'] = trim($content);
		$args['meta']    = $meta;
		$args['status']  = $status ? $status : 'unapproved';
		$args['parent']  = $parent;
		$args['user_id'] = $user_id;
		$args['type']    = trim($type);
	}



	/**
	 * check args and make where
	 *
	 * @param      <type>  $_args      The arguments
	 * @param      <type>  $where      The where
	 * @param      <type>  $_log_meta  The log meta
	 */
	public function comment_make_where($_args, &$where, $_log_meta)
	{
		$type = \lib\utility::request('type');
		if($type && is_string($type) || is_numeric($type))
		{
			$where['type'] = $type;
		}

		if(!$type && \lib\utility::isset_request('type'))
		{
			$where['type'] = null;
		}

		$user_id = \lib\utility::request('user_id');
		if($user_id && is_string($user_id) || is_numeric($user_id))
		{
			$where['user_id'] = \lib\coding::decode($user_id);
		}

		if(!$user_id && \lib\utility::isset_request('user_id'))
		{
			$where['user_id'] = null;
		}
	}
}
?>