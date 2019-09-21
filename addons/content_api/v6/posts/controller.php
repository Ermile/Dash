<?php
namespace content_api\v6\posts;


class controller
{
	public static function routing()
	{
		if(count(\dash\url::dir()) > 3 && \dash\url::subchild() !== 'get')
		{
			\content_api\v6::no(404);
		}

		if(!\dash\request::is('get'))
		{
			\content_api\v6::no(400);
		}

		if(\dash\url::subchild() === 'get')
		{
			$id = \dash\request::get('id');

			if(!$id || !\dash\coding::decode($id))
			{
				\content_api\v6::no(400);
			}

			$detail = self::load_post($id);

		}
		else
		{
			$detail = self::posts();
		}

		\content_api\v6::bye($detail);
	}


	private static function load_post($_id)
	{
		$load_post = \dash\app\posts::get($_id, ['check_login' => false]);
		return $load_post;
	}


	private static function posts()
	{
		$args = [];

		$limit = \dash\request::get('limit');

		if($limit && is_numeric($limit) && intval($limit) > 1 && intval($limit) < 100)
		{
			$args['limit'] = intval($limit);
		}

		$posts  = \dash\app\posts::get_post_list($args);

		return $posts;

	}



}
?>