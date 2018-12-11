<?php
namespace content_crm\sms\home;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSMS');

		\dash\data::page_title(T_("SMS Dashboard"));
		\dash\data::page_desc(T_("Check your sms setting and balance and quick navigate to every where"));

		\dash\data::page_pictogram('envelope-o');

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));

		$get_balance = \dash\session::get('sms_panel_detail');
		if(!$get_balance)
		{
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

			\dash\session::set('sms_panel_detail', $get_balance, null, (60 * 10));
		}

		\dash\data::SMSbalance($get_balance);

	}
}
?>