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

		$dataTable          = \dash\data::dataTable();
		$masterTicketDetail = \dash\data::masterTicketDetail();

		$msg = "#Ticket".$_id. "\n";

		if(isset($masterTicketDetail['title']))
		{
			$msg .= strip_tags($masterTicketDetail['title']). "-----\n";
		}

		if(isset($masterTicketDetail['content']))
		{
			$msg .= strip_tags($masterTicketDetail['content']). "\n";
		}

		if(isset($masterTicketDetail['datecreated']))
		{
			$msg .= \dash\datetime::fit($masterTicketDetail['datecreated']). "\n------";
		}

		if(is_array($dataTable))
		{
			foreach ($dataTable as $key => $value)
			{
				$msg .= "\n---------------\n";
				$msg .= @strip_tags($value['content']). "\n";
				$msg .= @\dash\datetime::fit($value['datecreated']). "\n";
			}
		}

		return $msg;
	}
}
?>