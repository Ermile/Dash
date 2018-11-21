<?php
namespace dash\app\log\caller\ticket;


class ticket_answerTicketAlert
{
	public static function site($_args = [])
	{
		$code = isset($_args['code']) ? $_args['code'] : null;

		$result              = [];
		$result['title']     = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);;
		$result['icon']      = 'life-ring';
		$result['cat']       = T_("Support");
		$result['iconClass'] = 'fc-red';


		$excerpt = '';
		$excerpt .=	'<a href="'.\dash\url::kingdom(). '/!'. $code. '">';
		$excerpt .= T_("Show ticket");
		$excerpt .= ' ';
		$excerpt .= \dash\utility\human::fitNumber($code, false);
		$excerpt .= '</a>';

		$result['txt'] = $excerpt;

		return $result;
	}


	public static function is_notif()
	{
		return true;
	}

}
?>