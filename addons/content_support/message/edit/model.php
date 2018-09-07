<?php
namespace content_support\message\edit;

class model
{




	public static function post()
	{

		// ready to insert comments
		$content = \dash\request::post('content');
		if(!trim($content))
		{
			\dash\notif::error(T_("Please fill the content"));
			return false;
		}

		$args =
		[
			'content' => $content,
		];

		\content_support\message\edit\view::config();

		\dash\db\comments::update($args, \dash\request::get('id'));

		\dash\notif::ok(T_("Ticket updated"));
		\dash\redirect::to(\dash\url::here().'/ticket/show?id='. \dash\request::get('id'));

	}
}
?>