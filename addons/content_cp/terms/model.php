<?php
namespace addons\content_cp\terms;


class model extends \addons\content_cp\main\model
{

	public function post_terms()
	{

		$post             = [];
		$post['title']    = \lib\utility::post('title');
		$post['desc']     = \lib\utility::post('desc');
		$post['excerpt']  = \lib\utility::post('excerpt');
		$post['parent']   = \lib\utility::post('parent');
		$post['language'] = \lib\define::get_language();
		$post['slug']     = \lib\utility::post('slug');
		$post['type']     = \lib\utility::get('type');
		$post['status']   = \lib\utility::post('status') ? 'enable' : 'disable' ;

		if(\lib\utility::get('edit'))
		{
			$post['id'] = \lib\utility::get('edit');
			\lib\app\term::edit($post);
		}
		else
		{
			\lib\app\term::add($post);
		}

		if(\lib\debug::$status)
		{
			if(\lib\utility::get('edit'))
			{
				\lib\debug::true(T_("Term successfully edited"));

				$url = \lib\url::here(). '/terms';

				if(\lib\utility::get('type'))
				{
					$url .= '?type='. \lib\utility::get('type');
				}

				$this->redirector($url);
			}
			else
			{
				\lib\debug::true(T_("Term successfully added"));
				$this->redirector(\lib\url::full());
			}
		}
	}
}
?>
