<?php
namespace content_api\v5\about;


class controller
{
	public static function routing()
	{
		$about = self::about();

		\content_api\v5::old_end5($about);
	}


	private static function about()
	{
		$about = [];
		$about_page_args =
		[
			'type'     => 'page',
			'status'   => 'publish',
			'slug'     => 'about',
			'language' => \dash\language::current(),
			'limit'    => 1,
		];

		$about_raw = \dash\db\posts::get($about_page_args);

		if($about_raw && is_array($about_raw))
		{
			foreach ($about_raw as $key => $value)
			{
				if(in_array($key, ['content', 'title', 'slug', 'language', 'url']))
				{
					$about[$key] = $value;
				}

				// if($key === 'content')
				// {
				// 	$strip_tags = str_replace("\n", " ", $strip_tags);
				// 	$strip_tags = str_replace("\t", " ", $strip_tags);
				// 	$strip_tags = str_replace("\r", " ", $strip_tags);
				// 	$strip_tags = str_replace("\s", " ", $strip_tags);
				// 	$strip_tags = strip_tags($value);
				// 	$about['content_raw'] = $strip_tags;
				// }
			}
		}

		if(is_callable(["\\lib\\app\\android", "about"]))
		{
			$my_about = \lib\app\android::about();
			if(is_array($my_about))
			{
				$about = array_merge($about, $my_about);
			}
		}


		return $about;
	}
}
?>