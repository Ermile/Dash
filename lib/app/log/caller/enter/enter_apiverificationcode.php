<?php
namespace dash\app\log\caller\enter;


class enter_apiverificationcode
{
	public static function site($_args = [])
	{
		$code = isset($_args['data']['mycode']) ? $_args['data']['mycode'] : null;
		$secret = isset($_args['data']['secret']) ? $_args['data']['secret'] : null;

		$code = \dash\utility\human::fitNumber($code, false);
		$result              = [];
		$result['title']     = T_("Verification code");
		$result['icon']      = 'log-in';
		$result['cat']      = T_("Enter");
		$result['iconClass'] = 'fc-blue';

		if($secret)
		{
			$excerpt = T_("The verification code has been sent to you");
		}
		else
		{
			$excerpt = T_("Your verification code is :mycode", ['mycode' => '<code>'. $code. '</code>']);
		}

		$result['excerpt'] = $excerpt;

		// $txt = '';
		// $txt .= T_("The validation code is made to enter your account");
		// $txt .= "<br>";
		// $txt .= T_("Be careful! if you did not request this code");
		// $txt .= "<br>";
		// $txt .= T_("Perhaps someone has entered your password correctly and intends to login to your account!");
		// $link = '<a href="'. \dash\url::kingdom(). '/account/profile/security">'. T_("here"). '</a>';
		// $txt .= "<br>";
		// $txt .= T_("You can see your active sessions :val", ['val' => $link]);
		// $txt .= "<br>";
		// $txt .= T_("Or change your password more securely.");

		// $result['txt'] = $txt;

		return $result;

	}

	public static function expire()
	{
		return date("Y-m-d H:i:s", time()+(60*5) ); // 5 min
	}

	public static function is_notif()
	{
		return true;
	}

	public static function sms()
	{
		return true;
	}

	public static function telegram()
	{
		return true;
	}


	public static function sms_text($_args, $_mobile)
	{
		$code = isset($_args['data']['mycode']) ? $_args['data']['mycode'] : null;
		$msg  = "code ". $code;

		$sms  =
		[
			'mobile' => $_mobile,
			'text'   => $msg,
			'meta'   =>
			[
				'footer' => false
			]
		];

		return json_encode($sms, JSON_UNESCAPED_UNICODE);
	}


	public static function telegram_text($_args, $_chat_id)
	{
		$code = isset($_args['data']['mycode']) ? $_args['data']['mycode'] : null;

		$text = '';
		$text .= T_("Your login code is :code", ['code' => \dash\utility\human::number($code)]);
		$text .= "\n\n". T_("This code can be used to log in to your account. Do not give it to anyone!");
		$text .= "\n" . T_("If you didn't request this code, ignore this message.");

		$tg                 = [];
		$tg['chat_id']      = $_chat_id;
		$tg['text']         = $text;
		$tg['reply_markup'] = false;

		return $tg;
	}

}
?>