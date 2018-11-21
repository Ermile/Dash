<?php
namespace dash\app\log\caller\su;

class su_upgradeDataBase
{
	public static function site()
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
		$tg_msg .= T_("Upgrade database");

		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $tg_msg;
		$tg['reply_markup'] = false;

		$tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>