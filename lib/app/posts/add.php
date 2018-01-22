<?php
namespace lib\app\posts;

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
			if($_option['debug']) \lib\debug::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		$args['user_id'] = \lib\user::id();

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}


		$return         = [];

		$post_id = \lib\db\posts::insert($args);

		if(!$post_id)
		{
			if($_option['save_log']) \lib\app::log('api:posts:no:way:to:insert:post', \lib\user::id(), \lib\app::log_meta());
			if($_option['debug']) \lib\debug::error(T_("No way to insert post"), 'db', 'system');
			return false;
		}

		$set_category = self::set_post_term($post_id, 'cat');

		$return['post_id'] = \lib\utility\shortURL::encode($post_id);

		if(\lib\debug::$status)
		{
			if($_option['debug']) \lib\debug::true(T_("Post successfuly added"));
		}

		return $return;
	}
}
?>