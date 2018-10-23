<?php
namespace dash\app\tg;


class ticket
{
	public static function answer($_id, $_answer)
	{
		// save answer
		\content_support\ticket\show\model::answer_save($_id, $_answer);
		return true;
	}


	public static function create($_title, $_content)
	{
		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => 'ticket',
			'content' => $_content,
			'title'   => $_title,
			'mobile'  => \dash\user::detail("mobile"),
			'file'    => null,
			'user_id' => \dash\user::id(),
		];
		\dash\notif::ok(T_("Your ticket was sended"));

		// insert comments
		$result = \dash\app\ticket::add($args);

		// \content_support\ticket\add\model::add_new($_title, $_content);
	}
}
?>