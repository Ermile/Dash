<?php
namespace dash\app\log\caller;

class answerTicketAlertSend
{
	public static function site()
	{
		return T_("Answer alert");
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
		$code = isset($_args['code']) ? $_args['code'] : null;
		$title    = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);

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
		$load = \dash\app\log\support_tools::load($_args);

		$code = isset($_args['code']) ? $_args['code'] : null;
		$title    = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= $title;
		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);

		// disable footer in sms
		// $msg['send_msg']['footer']   = false;

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $tg_msg;
		$tg['reply_markup'] = \dash\app\log\support_tools::tg_btn($code);

		$tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>