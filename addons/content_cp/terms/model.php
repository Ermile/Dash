<?php
namespace content_cp\terms;


class model
{
	public static function post()
	{
		if(\dash\request::post('action') === 'remove')
		{
			$term_id = \dash\request::get('edit');
			$term_id = \dash\coding::decode($term_id);
			if(!$term_id)
			{
				\dash\notif::error(T_("Invalid term id"));
				return false;
			}

			$load_term = \dash\db\terms::get(['id' => $term_id, 'limit' => 1]);
			if(!isset($load_term['type']))
			{
				\dash\notif::error(T_("Term id not found"));
				return false;
			}

			if($load_term['type'] === 'tag')
			{
				\dash\permission::access('cpTagDelete');
			}

			if($load_term['type'] === 'cat')
			{
				\dash\permission::access('cpCategoryDelete');
			}

			$remove = \dash\db\terms::remove($term_id);
			if($remove)
			{
				\dash\notif::warn(T_("Data successfully removed"));
				if(\dash\request::get('type'))
				{
					\dash\redirect::to(\dash\url::this(). '?type='. \dash\request::get('type'));
				}
				else
				{
					\dash\redirect::to(\dash\url::this());
				}
			}
			else
			{
				\dash\notif::error(T_("This term or tag used in post and can not delete it!"));
			}
			return;
		}

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
