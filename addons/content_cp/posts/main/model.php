<?php
namespace addons\content_cp\posts\main;


class model extends \mvc\model
{
	public static function upload_gallery()
	{
		if(\lib\utility::files('gallery'))
		{
			$uploaded_file = \lib\app\file::upload(['debug' => false, 'upload_name' => 'gallery']);

			if(isset($uploaded_file['url']))
			{
				// save uploaded file
				\lib\app\posts::post_gallery(\lib\request::get('id'), $uploaded_file['url'], 'add');
			}

			if(!\lib\debug::$status)
			{
				\lib\debug::error(T_("Can not upload file"));
			}
			else
			{
				\lib\debug::true(T_("File successfully uploaded"));
			}

			return true;
		}
		return false;

	}

	public static function remove_gallery()
	{
		$id = \lib\request::post('id');
		if(!is_numeric($id))
		{
			return false;
		}
		\lib\app\posts::post_gallery(\lib\request::get('id'), $id, 'remove');
		\lib\debug::msg('direct', true);
		(new \lib\redirector(\lib\url::full()))->redirect();

	}

	public static function getPost()
	{
		if(self::upload_gallery())
		{
			return false;
		}

		if(\lib\request::post('type') === 'remove_gallery')
		{
			self::remove_gallery();
			return false;
		}

		$post =
		[
			'id'          => \lib\request::get('id'),
			'subtitle'    => \lib\request::post('subtitle'),
			'excerpt'     => \lib\request::post('excerpt'),
			'title'       => \lib\request::post('title'),
			'tag'         => \lib\request::post('tag'),
			'slug'        => \lib\request::post('slug'),
			'content'     => isset($_POST['content']) ? $_POST['content'] : null,
			'publishdate' => \lib\request::post('publishdate'),
			'status'      => \lib\request::post('status'),
			'comment'     => \lib\request::post('comment'),
			'language'    => \lib\request::post('language'),
			'type'        => \lib\request::get('type'),
			'language'    => \lib\language::get_language(),
		];

		$all_post = \lib\request::post();

		$post['cat'] = [];

		foreach ($all_post as $key => $value)
		{
			if(substr($key, 0, 4) === 'cat_')
			{
				$post['cat'][] = substr($key, 4);
			}
		}

		if(\lib\utility::files('thumb'))
		{
			$uploaded_file = \lib\app\file::upload(['debug' => false, 'upload_name' => 'thumb']);

			if(isset($uploaded_file['url']))
			{
				$post['thumb'] = $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\lib\debug::$status)
			{
				return false;
			}
		}

		// var_dump($post);exit();
		return $post;

	}
}
?>
