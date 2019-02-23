<?php
namespace content_api\v6\contact;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		$contact = self::contact();

		\content_api\v6::bye($contact);
	}


	private static function contact()
	{
		$contact = [];
		$contact_page_args =
		[
			'type'     => 'page',
			'status'   => 'publish',
			'slug'     => 'vision',
			'language' => \dash\language::current(),
			'limit'    => 1,
		];

		$contact_raw = \dash\db\posts::get($contact_page_args);

		if($contact_raw && is_array($contact_raw))
		{
			foreach ($contact_raw as $key => $value)
			{
				if(in_array($key, ['content', 'title', 'slug', 'language', 'url']))
				{
					$contact[$key] = $value;
				}
			}
		}

		if(is_callable(["\\lib\\app\\android", "contact"]))
		{
			$my_contact = \lib\app\android::contact();
			if(is_array($my_contact))
			{
				$contact = array_merge($contact, $my_contact);
			}
		}


		return $contact;
	}
}
?>