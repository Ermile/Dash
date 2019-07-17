<?php
namespace content_api\v6;


class static_page
{

	public static function run($_type)
	{
		switch ($_type)
		{
			case 'contact':
			case 'about':
			case 'mission':
			case 'vision':
				$page = self::page($_type);
				\content_api\v6::bye($page);
				break;

			case 'news':
				$news = self::news();
				\content_api\v6::bye($news);
				break;

			default:
				\content_api\v6::no(404);
				break;
		}
	}


	private static function news()
	{
		$posts  = \dash\app\posts::get_post_list();
		$result = [];
		if(is_array($posts))
		{
			foreach ($posts as $index => $myPost)
			{
				foreach ($myPost as $field => $value)
				{
					switch ($field)
					{
						case 'id':
						case 'language':
						case 'title':
						case 'seotitle':
						case 'slug':
						case 'parent_url':
						case 'url':
						case 'excerpt':
						case 'subtitle':
						case 'content':
						case 'status':
						case 'publishdate':
						case 'datecreated':
							$result[$index][$field] = $value;
							break;
					}
				}
			}
		}

		return $result;

	}

	private static function page($_type)
	{
		$result = [];
		$result_page_args =
		[
			'type'     => 'page',
			'status'   => 'publish',
			'slug'     => $_type,
			'language' => \dash\language::current(),
			'limit'    => 1,
		];

		$result_raw = \dash\db\posts::get($result_page_args);

		if($result_raw && is_array($result_raw))
		{
			foreach ($result_raw as $key => $value)
			{
				switch ($key)
				{
					case 'content':
						$result[$key] = $value;

						break;

					case 'title':
					case 'slug':
					case 'language':
						$result[$key] = $value;
						break;

					case 'url':
						$result[$key] = \dash\url::kingdom(). '/'. $value;
						break;

					default:
						# code...
						break;
				}


			}
		}

		return $result;
	}
}
?>