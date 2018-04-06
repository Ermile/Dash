<?php
namespace addons\content_cp\terms;


class model extends \addons\content_cp\main\model
{

	public function post_terms()
	{

		$post             = [];
		$post['title']    = \dash\request::post('title');
		$post['desc']     = \dash\request::post('desc');
		$post['excerpt']  = \dash\request::post('excerpt');
		$post['parent']   = \dash\request::post('parent');
		$post['language'] = \dash\language::current();
		$post['slug']     = \dash\request::post('slug');
		$post['type']     = \dash\request::get('type');
		$post['status']   = \dash\request::post('status') ? 'enable' : 'disable' ;

		if(\dash\request::get('edit'))
		{
			$post['id'] = \dash\request::get('edit');
			\dash\app\term::edit($post);
		}
		else
		{
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
				\dash\redirect::to(\dash\url::full());
			}
		}
	}
}
?>
