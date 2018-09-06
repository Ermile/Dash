<?php
namespace content_support\ticket\edit;

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

		\content_support\ticket\edit\view::config();

		\dash\db\comments::update($args, \dash\request::get('id'));

		\dash\notif::ok(T_("Ticket updated"));
		\dash\redirect::to(\dash\url::this().'/show?id='. \dash\request::get('id'));

	}
}
?>