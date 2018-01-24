<?php
namespace addons\content_cp\posts\main;


class model extends \mvc\model
{
	public static function getPost()
	{
		$post =
		[
			'id'             => \lib\utility::get('id'),
			'title'          => \lib\utility::post('title'),
			'tag'            => \lib\utility::post('tag'),
			'slug'           => \lib\utility::post('slug'),
			'content'        => \lib\utility::post('content'),
			'publishdate'    => \lib\utility::post('publishdate'),
			'status'         => \lib\utility::post('status'),
			'comment'        => \lib\utility::post('comment'),
			'language'       => \lib\utility::post('language'),
			'type'           => \lib\utility::get('type'),
			'language'       => \lib\define::get_language(),
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
