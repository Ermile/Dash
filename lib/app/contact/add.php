<?php
namespace lib\app\user;
use \lib\utility;
use \lib\debug;

trait add
{

	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args = [])
	{
		\lib\app::variable($_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\app::request(),
			]
		];

		if(!\lib\user::id())
		{
			\lib\app::log('api:user:user_id:notfound', null, $log_meta);
			debug::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check();

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		$return = [];

		\lib\temp::set('last_user_added', isset($args['slug'])? $args['slug'] : null);

		$args['creator'] = \lib\user::id();
		$args['status']  = 'enable';
		$user_id = \lib\db\users::insert($args);

		if(!$user_id)
		{
			$args['slug'] = self::slug_fix($args);
			$user_id     = \lib\db\users::insert($args);
		}

		if(!$user_id)
		{
			\lib\app::log('api:user:no:way:to:insert:user', \lib\user::id(), $log_meta);
			debug::error(T_("No way to insert user"), 'db', 'system');
			return false;
		}

		$return['user_id'] = \lib\utility\shortURL::encode($user_id);
		$return['slug']     = $args['slug'];

		if(debug::$status)
		{
			debug::true(T_("User successfuly added"));
		}

		return $return;
	}


	/**
	 * fix duplicate slug
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function slug_fix($_args)
	{
		if(!isset($_args['slug']))
		{
			$_args['slug'] = (string) \lib\user::id(). (string) rand(1000,5000);
		}

		$new_slug     = null;
		$similar_slug = \lib\db\users::get_similar_slug($_args['slug']);
		$count        = count($similar_slug);
		$i            = 1;
		$new_slug     = (string) $_args['slug']. (string) ((int) $count +  (int) $i);
		while (in_array($new_slug, $similar_slug))
		{
			$i++;
			$new_slug = (string) $_args['slug']. (string) ((int) $count +  (int) $i);
		}

		\lib\temp::set('last_user_added', $new_slug);
		return $new_slug;
	}
}
?>