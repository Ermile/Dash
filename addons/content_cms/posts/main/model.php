<?php
namespace content_cms\posts\main;


class model
{
	public static function upload_gallery()
	{
		if(\dash\request::files('gallery'))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => 'gallery']);

			if(isset($uploaded_file['url']))
			{
				// save uploaded file
				\dash\app\posts::post_gallery(\dash\request::get('id'), $uploaded_file['url'], 'add');
			}

			if(!\dash\engine\process::status())
			{
				\dash\notif::error(T_("Can not upload file"));
			}
			else
			{
				\dash\notif::ok(T_("File successfully uploaded"));
			}

			return true;
		}
		return false;

	}

	public static function remove_gallery()
	{
		$id = \dash\request::post('id');
		if(!is_numeric($id))
		{
			return false;
		}

		\dash\app\posts::post_gallery(\dash\request::get('id'), $id, 'remove');
		\dash\redirect::pwd();
	}


	private static function remove_thumb()
	{
		if(\dash\request::post('deleteThumb'))
		{
			$id = \dash\request::get('id');

			\dash\app\posts::remove_thumb(\dash\request::get('id'));

			\dash\redirect::pwd();

			return true;
		}

		return false;
	}


	public static function getPost()
	{
		// check subdomain

		if(self::upload_gallery())
		{
			return false;
		}

		if(\dash\request::post('type') === 'remove_gallery')
		{
			self::remove_gallery();
			return false;
		}

		if(self::remove_thumb())
		{
			return false;
		}

		$post =
		[
			'id'          => \dash\request::get('id'),
			'subtitle'    => \dash\request::post('subtitle'),
			'excerpt'     => \dash\request::post('excerpt'),
			'title'       => \dash\request::post('title'),
			'tag'         => \dash\request::post('tag'),
			'slug'        => \dash\request::post('slug'),
			'content'     => isset($_POST['content']) ? $_POST['content'] : null,
			'publishdate' => \dash\request::post('publishdate'),
			'publishtime' => \dash\request::post('publishtime'),
			'status'      => \dash\request::post('status'),
			'comment'     => \dash\request::post('comment'),
			'language'    => \dash\request::post('language') ? \dash\request::post('language') : \dash\language::current(),
			'parent'      => \dash\request::post('parent'),
			'special'     => \dash\request::post('special'),
			'creator'     => \dash\request::post('creator'),
			'seotitle'    => \dash\request::post('seotitle'),
			'subtype'     => \dash\request::post('subtype'),
			'btntitle'    => \dash\request::post('btntitle'),
			'btnurl'      => \dash\request::post('btnurl') ? $_POST['btnurl'] : null,
			'btntarget'   => \dash\request::post('btntarget'),
			'btncolor'    => \dash\request::post('btncolor'),
			'srctitle'    => \dash\request::post('srctitle'),
			'srcurl'      => \dash\request::post('srcurl') ? $_POST['srcurl'] : null,
			'redirecturl' => \dash\request::post('redirecturl') ? $_POST['redirecturl'] : null,

		];



		if(\dash\url::subdomain())
		{
			$post['subdomain'] = \dash\url::subdomain();
		}


		if(!$post['status'])
		{
			$post['status'] = 'draft';
		}

		if(\dash\request::post('publishBtn') === 'publish')
		{
			$post['status'] = 'publish';
		}

		// if(!\dash\permission::check('cpPostsEditStatus'))
		// {
		// 	unset($post['status']);
		// }

		if(\dash\request::get('type'))
		{
			$post['type'] = \dash\request::get('type');
		}

		if(\dash\request::post('icon'))
		{
			$post['icon'] = \dash\request::post('icon');
		}

		$all_post = \dash\request::post();

		$post['cat'] = [];

		foreach ($all_post as $key => $value)
		{
			if(substr($key, 0, 4) === 'cat_')
			{
				$post['cat'][] = substr($key, 4);
			}
		}


		if(\dash\request::files('thumb'))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => 'thumb']);

			if(isset($uploaded_file['url']))
			{
				$post['thumb'] = $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\dash\engine\process::status())
			{
				return false;
			}
		}

		return $post;

	}
}
?>
