<?php
namespace addons\content_cp\terms;


class model extends \addons\content_cp\main\model
{

	public function post_terms()
	{

		$post             = [];
		$post['title']    = \lib\request::post('title');
		$post['desc']     = \lib\request::post('desc');
		$post['excerpt']  = \lib\request::post('excerpt');
		$post['parent']   = \lib\request::post('parent');
		$post['language'] = \lib\language::get_language();
		$post['slug']     = \lib\request::post('slug');
		$post['type']     = \lib\request::get('type');
		$post['status']   = \lib\request::post('status') ? 'enable' : 'disable' ;

		if(\lib\request::get('edit'))
		{
			$post['id'] = \lib\request::get('edit');
			\lib\app\term::edit($post);
		}
		else
		{
			\lib\app\term::add($post);
		}

		if(\lib\debug::$status)
		{
			if(\lib\request::get('edit'))
			{
				\lib\debug::true(T_("Term successfully edited"));

				$url = \lib\url::here(). '/terms';

				if(\lib\request::get('type'))
				{
					$url .= '?type='. \lib\request::get('type');
				}

				\lib\redirect::to($url);
			}
			else
			{
				\lib\debug::true(T_("Term successfully added"));
				\lib\redirect::to(\lib\url::full());
			}
		}
	}
}
?>
