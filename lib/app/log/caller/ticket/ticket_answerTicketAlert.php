<?php
namespace dash\app\log\caller\ticket;


class ticket_answerTicketAlert
{
	public static function site($_args = [])
	{
		$code = isset($_args['code']) ? $_args['code'] : null;
		$title    = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);
		return $title;
	}


	public static function is_notif()
	{
		return true;
	}

}
?>