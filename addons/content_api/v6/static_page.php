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



			default:
				\content_api\v6::no(404);
				break;
		}
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