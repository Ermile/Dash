<?php
namespace dash\app\log\caller;

class AnswerTicket
{
	public static function text()
	{
		return T_("Answer ticket");
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
		$plus = isset($load['data']['plus']) ? $load['data']['plus'] : null;
		$code = isset($_args['code']) ? $_args['code'] : null;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= " 💌". $plus;
		$tg_msg .= "\n🗣 ". \dash\log::from_name(). " #user". \dash\log::from_id();
		$tg_msg .= "\n—————\n📬 ";

		$title   = isset($load['title']) ? $load['title'] : null;
		$content = isset($load['content']) ? $load['content'] : null;
		$file    = isset($load['file']) ? $load['file'] : null;

		if($title)
		{
			$tg_msg .= $title . "\n";
		}

		if($content)
		{
			$content = \dash\app\log\msg::myStripTags($content);
			$tg_msg .= $content . "\n";
		}

		if($file)
		{
			$tg_msg .= $file . "\n";
		}

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