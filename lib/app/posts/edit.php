<?php
namespace dash\app\posts;


trait edit
{

	public static function edit($_args, $_option = [])
	{

		\dash\app::variable($_args, ['raw_field' => self::$raw_field]);

		$default_option =
		[
			'debug'    => true,
			'save_log' => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$id = \dash\app::request('id');
		$id = \dash\coding::decode($id);

		if(!$id)
		{
			\dash\app::log('api:posta:edit:permission:denide', \dash\user::id(), \dash\app::log_meta());
			\dash\notif::error(T_("Can not access to edit posta"), 'posta');
			return false;
		}

		$load_posts = \dash\db\posts::get(['id' => $id, 'limit' => 1]);

		if(!isset($load_posts['id']))
		{
			\dash\notif::error(T_("Invalid posts id"));
			return false;
		}

		if(array_key_exists('meta', $load_posts))
		{
			if(is_string($load_posts['meta']) && substr($load_posts['meta'], 0, 1) === '{')
			{
				$load_posts['meta'] = json_decode($load_posts['meta'], true);
			}
			elseif(is_array($load_posts['meta']))
			{
				// nothing
			}
			else
			{
				$load_posts['meta'] = [];
			}

			$_option['meta'] = $load_posts['meta'];
		}

		$_option['raw_args'] = $_args;

		// check args
		$args = self::check($id, $_option);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}

		if(!\dash\app::isset_request('subdomain')) unset($args['subdomain']);


		if(!$args['excerpt'])
		{
			$args['excerpt'] = \dash\utility\excerpt::extractRelevant($args['content']);
		}

		if(mb_strlen($args['excerpt']) >= 300)
		{
			$args['excerpt'] = substr($args['excerpt'], 0, 299);
		}

		if(in_array($args['type'], ['post', 'help', 'mag']))
		{
			if($args['type'] === 'help')
			{
				if(\dash\permission::check('cpTagHelpAdd'))
				{
					self::set_post_term($id, 'help_tag', 'posts', \dash\app::request('tag'));
				}
			}
			else
			{
				if(\dash\permission::check('cpTagAdd'))
				{
					self::set_post_term($id, 'tag');
				}
			}

			$post_url = self::set_post_term($id, 'cat');

			if($post_url !== false)
			{
				if($post_url)
				{
					$args['url'] = ltrim($post_url. '/'. $args['slug'], '/');
				}
				else
				{
					$args['url'] = $args['slug'];
				}
			}
		}

		if(!\dash\app::isset_request('type')) unset($args['type']);

		\dash\log::set('editPost', ['code' => $id, 'datalink' => \dash\coding::encode($id)]);

		\dash\db\posts::update($args, $id);


		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Post successfully updated"));
		}
	}
}
?>