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

		// check args
		$args = self::check($id, $_option);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		if($args['type'] === 'post')
		{
			self::set_post_term($id, 'tag');

			$post_url = self::set_post_term($id, 'cat');

			if($post_url !== false)
			{
				if($post_url)
				{
					$args['url'] = $post_url. '/'. $args['slug'];
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