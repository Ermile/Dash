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
		\content_support\ticket\add\model::add_new('telegram', $_content);
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

		\content_support\ticket\show\view::see_ticket($masterTicketDetail, $dataTable, $_id);

		$msg = '';
		$msg .= "🆔#Ticket".$_id. "\n";

		if(is_array($dataTable))
		{
			foreach ($dataTable as $key => $value)
			{
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
					$msg .= "\n⏳ ". \dash\datetime::fit($value['datecreated'], true);
				}
				$msg .= "\n—————\n ";
			}
		}

		return $msg;
	}
}
?>