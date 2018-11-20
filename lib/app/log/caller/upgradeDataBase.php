<?php
namespace dash\app\log\caller;

class upgradeDataBase
{
	public static function text()
	{
		return T_("Upgrade database");
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
		$load = \dash\app\log\support_tools::load($_args);
		$plus = \dash\app\log\support_tools::plus($_args);

		$code = isset($_args['code']) ? $_args['code'] : null;

		$tg_msg = '';
		$tg_msg .= "🛢#DbUpgrade\n👨‍💻 ".\dash\log::from_name()."\n";
		$tg_msg .= self::text();

		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $tg_msg;
		$tg['reply_markup'] = \dash\app\log\support_tools::tg_btn($code);

		$tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>