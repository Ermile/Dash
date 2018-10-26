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
		$_id = \dash\utility\convert::to_en_number($_id);
		\content_support\ticket\show\view::load_tichet($_id);

		$dataTable          = \dash\data::dataTable();
		$masterTicketDetail = \dash\data::masterTicketDetail();

		if(!$dataTable)
		{
			return false;
		}

		$msg = '';
		$msg .= "🆔#Ticket".$_id. "\n";
		// $msg .= " #New \n🗣 ". \dash\data::masterTicketDetail_displayname(). " #user". \dash\data::masterTicketDetail_user_id();
		// $msg .= "\n—————\n📬 ";

		// if(isset($masterTicketDetail['title']))
		// {
		// 	$msg .= strip_tags($masterTicketDetail['title']). "\n";
		// }

		// if(isset($masterTicketDetail['content']))
		// {
		// 	$msg .= strip_tags($masterTicketDetail['content']). "\n";
		// }

		// if(isset($masterTicketDetail['datecreated']))
		// {
		// 	$msg .= "\n⏳ ". \dash\datetime::fit($masterTicketDetail['datecreated'], true);
		// }

		if(is_array($dataTable))
		{
			foreach ($dataTable as $key => $value)
			{
				// $key_fit = \dash\utility\human::fitNumber($key + 1);
				// $msg .= "🔄 $key_fit\n"
				$msg .= "🗣 ". @$value['displayname']. " #user". @$value['user_id'];

				if(isset($value['title']))
				{
					$msg .= "\n📬 ";
					$msg .= "<b>". strip_tags($value['title']). "</b>";
				}

				if(isset($value['content']))
				{
					$msg .= "\n". strip_tags($value['content']). "\n";
				}

				if(isset($value['datecreated']))
				{
					$msg .= "\n⏳ ". \dash\datetime::fit($value['datecreated'], 'shortDate');
				}
				$msg .= "\n—————\n ";
			}
		}

		return $msg;
	}
}
?>