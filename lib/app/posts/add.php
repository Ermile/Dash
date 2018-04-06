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
		$content = isset($_args['content']) ? $_args['content'] : null;
		$content = addslashes($content);

		\lib\app::variable($_args);

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

		if(!\lib\user::id())
		{
			if($_option['save_log']) \lib\app::log('api:posts:user_id:notfound', null, \lib\app::log_meta());
			if($_option['debug']) \lib\notif::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		$args['user_id'] = \lib\user::id();

		if($args === false || !\lib\engine\process::status())
		{
			return false;
		}

		if(array_key_exists('content', $args))
		{
			$args['content'] = $content;
		}

		if(!$args['excerpt'])
		{
			$args['excerpt'] = \lib\utility\excerpt::extractRelevant($content);
		}

		if(mb_strlen($args['excerpt']) > 300)
		{
			$args['excerpt'] = substr($args['excerpt'], 0, 300);
		}

		$return         = [];

		$post_id = \lib\db\posts::insert($args);

		if(!$post_id)
		{
			if($_option['save_log']) \lib\app::log('api:posts:no:way:to:insert:post', \lib\user::id(), \lib\app::log_meta());
			if($_option['debug']) \lib\notif::error(T_("No way to insert post"), 'db', 'system');
			return false;
		}

		if($args['type'] === 'post')
		{
			self::set_post_term($post_id, 'tag');

			$post_url = self::set_post_term($post_id, 'cat');

			if($post_url !== false)
			{

				if($post_url)
				{
					\lib\db\posts::update(['url' => $args['slug']], $post_id);
				}
				else
				{
					\lib\db\posts::update(['url' => ltrim($post_url. '/'. $args['slug'], '/')], $post_id);
				}
			}
		}

		$return['post_id'] = \lib\coding::encode($post_id);

		if(\lib\engine\process::status())
		{
			if($_option['debug']) \lib\notif::ok(T_("Post successfuly added"));
		}

		return $return;
	}
}
?>