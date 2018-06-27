<?php
namespace content_n\home;

class controller
{
	public static function routing()
	{

		$module = \dash\url::module();
		if(!$module)
		{
			\dash\redirect::to(\dash\url::base());
		}

		$module = \dash\coding::decode($module);

		if(!$module)
		{
			\dash\redirect::to(\dash\url::base());
		}

		$load_post = \dash\app\posts::get(\dash\url::module());

		if(!isset($load_post['type']) || !isset($load_post['status']) || !isset($load_post['url']))
		{
			\dash\redirect::to(\dash\url::base());
		}

		if(!in_array($load_post['type'], ['post', 'page']))
		{
			\dash\redirect::to(\dash\url::base());
		}

		if(!in_array($load_post['status'], ['publish']))
		{
			\dash\redirect::to(\dash\url::base());
		}

		\dash\redirect::to(\dash\url::base().'/'. $load_post['url']);

	}
}
?>