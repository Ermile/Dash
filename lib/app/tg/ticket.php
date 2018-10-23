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


	public static function create($_content)
	{
		// $args =
		// [
		// 	'author'  => \dash\user::detail('displayname'),
		// 	'email'   => \dash\user::detail('email'),
		// 	'type'    => 'ticket',
		// 	'content' => $_content,
		// 	'title'   => T_("Ticket via telegram"),
		// 	'mobile'  => \dash\user::detail("mobile"),
		// 	'file'    => null,
		// 	'user_id' => \dash\user::id(),
		// ];

		// // insert comments
		// $result = \dash\app\ticket::add($args);

		\content_support\ticket\add\model::add_new(T_("Ticket via telegram"), $_content);
	}


	public static function list($_id)
	{
		\content_support\ticket\show\view::load_tichet($_id);
		$dataTable = \dash\data::dataTable();

		$msg = T_("Start"). "\n";

		if(is_array($dataTable))
		{
			foreach ($dataTable as $key => $value)
			{
				$msg .= @$value['content']. "\n";
			}
		}

		return $msg;
	}
}
?>