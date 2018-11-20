<?php
namespace dash\app\log\caller;

class blockIPDetected
{
	public static function text()
	{
		return T_("A new Block ip detected");
	}

	public static function send_to()
	{
		return ['supervisor'];
	}

	public static function is_notif()
	{
		return true;
	}

	public static function telegram()
	{
		return true;
	}

	public static function telegram_text($_args, $_chat_id)
	{
		$ip = isset($_args['ip']) ? $_args['ip'] : null;

		$tg_msg = '';
		$tg_msg .= "#BlockIP \nA new Block ip detected \n".$ip;
		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);
		#
		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['caption']      = $tg_msg;
		$tg['method']       = 'sendDocument';
		$tg['document']     = "https://media.giphy.com/media/RAnjqIezKiIEM/giphy.gif";
		$tg['reply_markup'] = \dash\app\log\support_tools::tg_btn2($code);

		$tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>