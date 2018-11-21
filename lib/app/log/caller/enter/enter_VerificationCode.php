<?php
namespace dash\app\log\caller\enter;


class enter_VerificationCode
{
	public static function site($_args = [])
	{
		$code = isset($_args['data']['mycode']) ? $_args['data']['mycode'] : null;
		$code = \dash\utility\human::fitNumber($code, false);
		$result              = [];
		$result['title']     = T_("Verification code");
		$result['icon']      = 'log-in';
		$result['cat']      = T_("Enter");
		$result['iconClass'] = 'fc-green';

		$excerpt = T_("Your verification code is :mycode", ['mycode' => '<code>'. $code. '</code>']);
		$result['excerpt'] = $excerpt;

		$txt = '';
		$txt .= T_("The validation code is made to enter your account");
		$txt .= "<br>";
		$txt .= T_("Be careful! if you did not request this code");
		$txt .= "<br>";
		$txt .= T_("Perhaps someone has entered your password correctly and intends to login to your account!");
		$link = '<a href="'. \dash\url::kingdom(). '/account/profile/security">'. T_("here"). '</a>';
		$txt .= "<br>";
		$txt .= T_("You can see your active sessions :val", ['val' => $link]);
		$txt .= "<br>";
		$txt .= T_("Or change your password more securely.");

		$result['txt'] = $txt;

		return $result;

	}

	public static function send_to_creator()
	{
		return true;
	}

	public static function is_notif()
	{
		return true;
	}

}
?>