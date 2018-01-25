<?php
namespace lib\app\posts;
use \lib\debug;

trait edit
{
	/**
	 * edit a user
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function edit($_args, $_option = [])
	{
		$content = isset($_args['content']) ? $_args['content'] : null;
		$content = addslashes($content);

		\lib\app::variable($_args);

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

		$id = \lib\app::request('id');
		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			\lib\app::log('api:posta:edit:permission:denide', \lib\user::id(), \lib\app::log_meta());
			\lib\debug::error(T_("Can not access to edit posta"), 'posta');
			return false;
		}

		$load_posts = \lib\db\posts::get(['id' => $id, 'limit' => 1]);

		if(!isset($load_posts['id']))
		{
			\lib\debug::error(T_("Invalid posts id"));
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

		// check args
		$args = self::check($id, $_option);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		if(array_key_exists('content', $args))
		{
			$args['content'] = $content;
		}

		if($args['type'] === 'post')
		{
			self::set_post_term($id, 'tag');

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

		\lib\db\posts::update($args, $id);


		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("Post successfully updated"));
		}
	}
}
?>