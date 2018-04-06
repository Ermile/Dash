<?php
namespace dash\app\posts;


trait get
{

	public static function get_category_tag($_post_id, $_type)
	{
		$post_id = \lib\coding::decode($_post_id);
		if(!$post_id)
		{
			return false;
		}

		$result = \lib\db\termusages::usage($post_id, $_type);

		$temp = [];
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$temp[] = self::ready($value);
			}
		}

		return $temp;
	}

	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function get($_id, $_options = [])
	{
		$default_options =
		[
			'debug'          => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(!\lib\user::id())
		{
			return false;
		}

		$id = \lib\coding::decode($_id);

		if(!$id)
		{
			\lib\notif::error(T_("Invalid posts id"));
			return false;
		}

		$detail = \lib\db\posts::get(['id' => $id, 'limit' => 1]);

		$temp = [];

		if(is_array($detail))
		{
			$temp = self::ready($detail);
		}

		return $temp;
	}


	public static function get_post_list($_options = [])
	{
		$default_options =
		[
			'limit'      => 10,
			'cat'        => null,
			'tag'        => null,
			'term'       => null,
			'pagenation' => false,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if($_options['cat'])
		{
			$get_last_posts = \lib\db\posts::get_posts_term($_options, 'cat');
		}
		elseif($_options['tag'])
		{
			$get_last_posts = \lib\db\posts::get_posts_term($_options, 'tag');
		}
		elseif($_options['term'])
		{
			$_options['term'] = \lib\coding::decode($_options['term']);
			$get_last_posts   = \lib\db\posts::get_posts_term($_options, 'term');
		}
		else
		{
			$get_last_posts = \lib\db\posts::get_last_posts($_options);
		}

		$temp = [];
		if(is_array($get_last_posts))
		{
			foreach ($get_last_posts as $key => $value)
			{
				$temp[] = self::ready($value);
			}
		}
		return $temp;
	}
}
?>