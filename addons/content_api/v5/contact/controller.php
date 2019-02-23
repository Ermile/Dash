<?php
namespace content_api\v5\contact;


class controller
{
	public static function routing()
	{
		$contact = self::contact();

		\content_api\v5::old_end5($contact);
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

				// if($key === 'content')
				// {
				// 	$strip_tags = str_replace("\n", " ", $strip_tags);
				// 	$strip_tags = str_replace("\t", " ", $strip_tags);
				// 	$strip_tags = str_replace("\r", " ", $strip_tags);
				// 	$strip_tags = str_replace("\s", " ", $strip_tags);
				// 	$strip_tags = strip_tags($value);
				// 	$contact['content_raw'] = $strip_tags;
				// }
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