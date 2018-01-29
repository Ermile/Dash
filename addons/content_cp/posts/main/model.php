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
				\lib\app\posts::post_gallery(\lib\utility::get('id'), $uploaded_file['url'], 'add');
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
		$id = \lib\utility::post('id');
		if(!is_numeric($id))
		{
			return false;
		}
		\lib\app\posts::post_gallery(\lib\utility::get('id'), $id, 'remove');
		\lib\debug::msg('direct', true);
		(new \lib\redirector(\lib\url::full()))->redirect();

	}

	public static function getPost()
	{
		if(self::upload_gallery())
		{
			return false;
		}

		if(\lib\utility::post('type') === 'remove_gallery')
		{
			self::remove_gallery();
			return false;
		}

		$post =
		[
			'id'          => \lib\utility::get('id'),
			'subtitle'    => \lib\utility::post('subtitle'),
			'excerpt'     => \lib\utility::post('excerpt'),
			'title'       => \lib\utility::post('title'),
			'tag'         => \lib\utility::post('tag'),
			'slug'        => \lib\utility::post('slug'),
			'content'     => isset($_POST['content']) ? $_POST['content'] : null,
			'publishdate' => \lib\utility::post('publishdate'),
			'status'      => \lib\utility::post('status'),
			'comment'     => \lib\utility::post('comment'),
			'language'    => \lib\utility::post('language'),
			'type'        => \lib\utility::get('type'),
			'language'    => \lib\define::get_language(),
		];

		$all_post = \lib\utility::post();

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
