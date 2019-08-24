<?php
namespace dash\app\log\caller\ticket;


class ticket_answerTicketAlertSend
{
	public static function site($_args = [])
	{
		$masterid =  \dash\app\log\support_tools::masterid($_args);

		$result              = [];
		$result['title']     = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($masterid, false)]);;
		$result['icon']      = 'life-ring';
		$result['cat']       = T_("Support");
		$result['iconClass'] = 'fc-green';


		$excerpt = '';
		$excerpt .=	'<a href="'.\dash\url::kingdom(). '/!'. $masterid. '">';
		$excerpt .= T_("Show ticket");
		$excerpt .= ' ';
		$excerpt .= \dash\utility\human::fitNumber($masterid, false);
		$excerpt .= '</a>';

		$result['txt'] = $excerpt;

		return $result;
	}

	public static function expire()
	{
		return date("Y-m-d H:i:s", strtotime("+100 days"));
	}


	public static function is_notif()
	{
		return true;
	}

	public static function telegram()
	{
		return true;
	}

	public static function sms()
	{
		return true;
	}

	public static function sms_text($_args, $_mobile)
	{
		$masterid  =  \dash\app\log\support_tools::masterid($_args);
		$title = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($masterid, false)]);
		$title .= "\n";
		$title .= \dash\url::domain(). '/!'. $masterid;

		$sms =
		[
			'mobile' => $_mobile,
			'text'   => $title,
			'meta'   =>
			[
				'footer' => false
			]
		];

		return json_encode($sms, JSON_UNESCAPED_UNICODE);
	}


	public static function telegram_text($_args, $_chat_id)
	{
		$load  = \dash\app\log\support_tools::load($_args);
		$masterid  = \dash\app\log\support_tools::masterid($_args);
		$title = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($masterid, false)]);

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$masterid;
		$tg_msg .= "\n". $title;
		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);

		// disable footer in sms
		// $msg['send_msg']['footer']   = false;

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $tg_msg;
		$tg['reply_markup'] = \dash\app\log\support_tools::tg_btn2($masterid);

		// $tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>