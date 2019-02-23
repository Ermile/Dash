<?php
namespace content_api\v5\vision;


class controller
{
	public static function routing()
	{
		$vision = self::vision();

		\content_api\v5::old_end5($vision);
	}


	private static function vision()
	{
		$vision = [];
		$vision_page_args =
		[
			'type'     => 'page',
			'status'   => 'publish',
			'slug'     => 'vision',
			'language' => \dash\language::current(),
			'limit'    => 1,
		];

		$vision_raw = \dash\db\posts::get($vision_page_args);

		if($vision_raw && is_array($vision_raw))
		{
			foreach ($vision_raw as $key => $value)
			{
				if(in_array($key, ['content', 'title', 'slug', 'language', 'url']))
				{
					$vision[$key] = $value;
				}

				// if($key === 'content')
				// {
				// 	$strip_tags = str_replace("\n", " ", $strip_tags);
				// 	$strip_tags = str_replace("\t", " ", $strip_tags);
				// 	$strip_tags = str_replace("\r", " ", $strip_tags);
				// 	$strip_tags = str_replace("\s", " ", $strip_tags);
				// 	$strip_tags = strip_tags($value);
				// 	$vision['content_raw'] = $strip_tags;
				// }
			}
		}

		if(is_callable(["\\lib\\app\\android", "vision"]))
		{
			$my_vision = \lib\app\android::vision();
			if(is_array($my_vision))
			{
				$vision = array_merge($vision, $my_vision);
			}
		}


		return $vision;
	}
}
?>