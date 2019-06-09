<?php
namespace content_cms\sitemap;

class model
{
	public static function post()
	{
		if(\dash\request::post('run') === 'yes')
		{
			$result = \dash\utility\sitemap::create();
			\dash\session::set('result_create_sitemap' , $result);
			\dash\notif::ok(T_("Sitemap successfully created"));
			\dash\redirect::pwd();
			return;
		}


		if(\dash\request::post('remove') === 'remove')
		{
			\dash\utility\sitemap::delete();
			\dash\redirect::pwd();

		}
	}
}
?>