<?php
namespace dash\app\posts;

trait add
{

	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{

		\dash\app::variable($_args, ['raw_field' => self::$raw_field]);

		$default_option =
		[
			'save_log' => true,
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		if(!\dash\user::id())
		{
			if($_option['save_log']) \dash\app::log('api:posts:user_id:notfound', null, \dash\app::log_meta());
			if($_option['debug']) \dash\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check(null, $_option);

		$args['user_id'] = \dash\user::id();

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		if(!array_key_exists('status', $args))
		{
			$args['status'] = 'draft';
		}

		if(!$args['excerpt'])
		{
			$args['excerpt'] = \dash\utility\excerpt::extractRelevant($args['content']);
		}

		if(mb_strlen($args['excerpt']) > 300)
		{
			$args['excerpt'] = substr($args['excerpt'], 0, 300);
		}

		$return         = [];

		$post_id = \dash\db\posts::insert($args);

		if(!$post_id)
		{
			if($_option['save_log']) \dash\app::log('api:posts:no:way:to:insert:post', \dash\user::id(), \dash\app::log_meta());
			if($_option['debug']) \dash\notif::error(T_("No way to insert post"), 'db', 'system');
			return false;
		}


		if(in_array($args['type'], ['post', 'help', 'mag']))
		{

			if($args['type'] === 'help')
			{
				if(\dash\permission::check('cpTagHelpAdd'))
				{
					self::set_post_term($post_id, 'help_tag', 'posts', \dash\app::request('tag'));
				}
			}

			else
			{
				if(\dash\permission::check('cpTagAdd'))
				{
					self::set_post_term($post_id, 'tag');
				}
			}


			if($args['type'] === 'mag')
			{
				$post_url = self::set_post_term($post_id, 'mag', null, \dash\app::request('cat'));
			}
			else
			{
				$post_url = self::set_post_term($post_id, 'cat');
			}


			if($post_url !== false)
			{

				if($post_url)
				{
					\dash\db\posts::update(['url' => ltrim($post_url. '/'. $args['slug'], '/')], $post_id);
				}
				else
				{
					\dash\db\posts::update(['url' => $args['slug']], $post_id);
				}
			}
		}

		$return['post_id'] = \dash\coding::encode($post_id);
		\dash\log::set('addNewPost', ['code' => $post_id, 'datalink' => $return['post_id']]);

		if(\dash\engine\process::status())
		{
			if($_option['debug']) \dash\notif::ok(T_("Post successfuly added"));
		}

		return $return;
	}
}
?>