<?php
namespace dash\app\tg;


class ticket
{
	public static function answer($_id, $_answer)
	{
		// save answer

		// ready to insert comments
		$args =
		[
			'type'    => 'ticket',
			'content' => $_id,
			'user_id' => \dash\user::id(true),
			'parent'  => $_id,

		];

		$result = \dash\app\ticket::add($args);

		return true;
	}
}
?>