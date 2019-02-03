<?php
namespace dash\app\log\caller\su;

class su_dayEventReport
{
	public static function site($_args = [])
	{
		$sum_diff = isset($_args['data']['result']['sum_diff']) ? $_args['data']['result']['sum_diff'] : 0;
		$excerpt  = '<span class="fc-green">'. \dash\utility\human::fitNumber($sum_diff). '</span> ';
		$excerpt .= T_("new record!");

		$result              = [];
		$result['title']     = T_("What was yesterday");
		$result['icon']      = 'report';
		$result['cat']       = T_("System");
		$result['iconClass'] = 'fc-blue';
		$result['excerpt']   = $excerpt;

		if(isset($_args['data']['result']) && is_array($_args['data']['result']))
		{
			$txt = '';
			foreach ($_args['data']['result'] as $key => $value)
			{
				if(!is_array($value))
				{
					continue;
				}

				if(!array_key_exists('now', $value) || !array_key_exists('daydiff', $value))
				{
					continue;
				}
				if(!$value['daydiff'] || !$value['now'])
				{
					continue;
				}
				$txt .= T_($key). " ". \dash\utility\human::fitNumber($value['now']). " <br> +".\dash\utility\human::fitNumber($value['daydiff'])." <hr>";
			}
			$result['txt']   = $txt;
		}

		return $result;
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

	public static function expire()
	{
		return date("Y-m-d H:i:s", strtotime("+30 days"));
	}



	public static function telegram_text($_args, $_chat_id)
	{
		$sum_diff = isset($_args['data']['result']['sum_diff']) ? $_args['data']['result']['sum_diff'] : 0;

		$tg_msg  = '';
		$tg_msg .= "📺#ReportDaily \n";
		$tg_msg .= T_("What was yesterday"). "\n";
		$tg_msg .= \dash\utility\human::fitNumber($sum_diff);;
		$tg_msg .= ' '. T_("new record!"). "\n";


		if(isset($_args['data']['result']) && is_array($_args['data']['result']))
		{
			foreach ($_args['data']['result'] as $key => $value)
			{
				if(!is_array($value))
				{
					continue;
				}
				if(!array_key_exists('now', $value) || !array_key_exists('daydiff', $value))
				{
					continue;
				}
				if(!$value['daydiff'] || !$value['now'])
				{
					continue;
				}
				$tg_msg .= T_($key). " -> ". \dash\utility\human::fitNumber($value['now']). " | +".\dash\utility\human::fitNumber($value['daydiff'])." \n";
			}

		}

		$tg_msg .= "\n⏳ ". \dash\datetime::fit(date("Y-m-d H:i:s"), true);

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $tg_msg;
		$tg['reply_markup'] = false;

		// $tg = json_encode($tg, JSON_UNESCAPED_UNICODE);

		return $tg;
	}
}
?>