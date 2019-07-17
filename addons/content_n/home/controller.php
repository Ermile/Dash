<?php
namespace content_n\home;

class controller
{
	public static function routing()
	{

		$module = \dash\url::module();
		if(!$module)
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		$module = \dash\coding::decode($module);

		if(!$module)
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		$load_post = \dash\app\posts::get(\dash\url::module(), ['check_login' => false]);

		if(!isset($load_post['type']) || !isset($load_post['status']) || !isset($load_post['url']) || !isset($load_post['language']))
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		if(!in_array($load_post['type'], ['post', 'page', 'help']))
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		if(!in_array($load_post['status'], ['publish']))
		{
			\dash\redirect::to(\dash\url::kingdom());
		}

		if($load_post['type'] === 'help')
		{
			$load_post['url'] = 'support/'. $load_post['url'];
		}

		$url = \dash\url::base().'/'. $load_post['language']. '/'. $load_post['url'];

		\dash\log::set('newsCodeRedirect', ['code' => \dash\url::module(), 'link' => $url]);

		\dash\redirect::to($url);

	}
}
?>