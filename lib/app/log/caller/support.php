<?php
namespace dash\app\log\msg;

class support
{


	public static function answerTicketAlertSend($_args, $_user)
	{
		$code = isset($_args['code']) ? $_args['code']: null;

		$msg             = [];
		$msg['title']    = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);
		$msg['content']  = T_(":val answer your ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram'] = true;
		$msg['sms']      = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= "\n". T_("Regards"). "\n";
		$tg_msg .= "\n". T_("Ticket :val answered", ['val' => $code]). "\n";

		if(isset($_args['datecreated']))
		{
			$tg_msg .= "\n⏳ ". \dash\datetime::fit($_args['datecreated'], true);
		}

		$sms_msg = $msg['title']. "\n" . \dash\url::domain().'/!'. $code;

		$msg['send_msg']             = [];
		$msg['send_msg']['telegram'] = $tg_msg;

		// disable footer in sms
		$msg['send_msg']['footer']   = false;

		$msg['send_msg']['sms']      = $sms_msg;

		return $msg;
	}


	public static function answerTicketAlert($_args, $_user)
	{
		$msg             = self::answerTicketAlertSend($_args, $_user);
		$msg['telegram'] = false;
		$msg['sms']      = false;
		return $msg;
	}


}
?>