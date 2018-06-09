<?php
namespace content_cp\terms;


class model
{
	public static function post()
	{

		$post             = [];
		$post['title']    = \dash\request::post('title');
		$post['desc']     = \dash\request::post('desc');
		$post['excerpt']  = \dash\request::post('excerpt');
		$post['parent']   = \dash\request::post('parent');
		$post['language'] = \dash\request::post('language');;
		$post['slug']     = \dash\request::post('slug');
		$post['type']     = \dash\request::get('type');
		$post['status']   = \dash\request::post('status') ? 'enable' : 'disable' ;

		$myType = \dash\request::get('type');

		if(\dash\request::get('edit'))
		{
			if($myType)
			{
				switch ($myType)
				{
					case 'cat':
					case 'category':
						\dash\permission::access('cpCategoryEdit');
						break;

					case 'tag':
						\dash\permission::access('cpTagEdit');
						break;
				}
			}
			else
			{
				\dash\permission::access('cpTagEdit');
			}

			$post['id'] = \dash\request::get('edit');
			\dash\app\term::edit($post);
		}
		else
		{
			if($myType)
			{
				switch ($myType)
				{
					case 'cat':
					case 'category':
						\dash\permission::access('cpCategoryAdd');
						break;

					case 'tag':
						\dash\permission::access('cpTagAdd');
						break;
				}
			}
			else
			{
				\dash\permission::access('cpTagAdd');
			}

			\dash\app\term::add($post);
		}

		if(\dash\engine\process::status())
		{
			if(\dash\request::get('edit'))
			{
				\dash\notif::ok(T_("Term successfully edited"));

				$url = \dash\url::here(). '/terms';

				if(\dash\request::get('type'))
				{
					$url .= '?type='. \dash\request::get('type');
				}

				\dash\redirect::to($url);
			}
			else
			{
				\dash\notif::ok(T_("Term successfully added"));
				\dash\redirect::to(\dash\url::pwd());
			}
		}
	}
}
?>
