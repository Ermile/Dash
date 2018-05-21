<?php
namespace content_cp\sms\home;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSMS');

		\dash\data::page_title(T_("SMS Dashboard"));
		\dash\data::page_desc(T_("Check your sms setting and balance and quick navigate to every where"));


		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));
		$default =
		[
			'remaincredit' => null,
			'expiredate'   => null,
			'type'         => 'Unknow',
		];
		$get_balance = \dash\utility\sms::info();

		if(is_array($get_balance))
		{
			$get_balance = array_merge($default, $get_balance);
		}

		\dash\data::SMSbalance($get_balance);

	}
}
?>