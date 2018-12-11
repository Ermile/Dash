<?php
namespace content_crm\sms\verify;

class view
{
	public static function config()
	{
		\dash\permission::access('cpSmsSend');

		\dash\data::page_title(T_("Send Sms verify check"));
		// \dash\data::page_desc(T_("Send every sms to every user by mobile"));
		\dash\data::page_pictogram('envelope-o');

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Dashboard'));

		\dash\data::bodyclass('unselectable');
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);

		$sms = \dash\session::get('verify_sms_send');

		if(!$sms)
		{
			\dash\header::status(404, T_("Sms detail not found"));
		}

		if(isset($sms['mobile']) && is_array($sms['mobile']))
		{
			$sms['count'] = count($sms['mobile']);
		}
		\dash\data::sms($sms);

	}
}
?>