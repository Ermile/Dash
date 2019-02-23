<?php
namespace content_api\v6\mission;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		$mission = self::mission();

		\content_api\v6::bye($mission);
	}


	private static function mission()
	{
		$mission = [];
		$mission_page_args =
		[
			'type'     => 'page',
			'status'   => 'publish',
			'slug'     => 'mission',
			'language' => \dash\language::current(),
			'limit'    => 1,
		];

		$mission_raw = \dash\db\posts::get($mission_page_args);

		if($mission_raw && is_array($mission_raw))
		{
			foreach ($mission_raw as $key => $value)
			{
				if(in_array($key, ['content', 'title', 'slug', 'language', 'url']))
				{
					$mission[$key] = $value;
				}
			}
		}

		if(is_callable(["\\lib\\app\\android", "mission"]))
		{
			$my_mission = \lib\app\android::mission();
			if(is_array($my_mission))
			{
				$mission = array_merge($mission, $my_mission);
			}
		}


		return $mission;
	}
}
?>