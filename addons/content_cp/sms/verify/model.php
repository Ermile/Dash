<?php
namespace content_cp\sms\verify;


class model
{
	public static function post()
	{
		\dash\permission::access('cpSmsSend');

		$sms = \dash\session::get('verify_sms_send');
		\dash\session::set('verify_sms_send', null);
		if(\dash\request::post('ok'))
		{
			if(isset($sms['msg']) && isset($sms['mobile']))
			{
				if(is_array($sms['mobile']))
				{
					\dash\utility\sms::send_array($sms['mobile'], $sms['msg']);
					\dash\notif::ok(T_("SMS was sended to :count unique mobile number", ['count' => count($sms['mobile'])]));
				}
				else
				{
					\dash\utility\sms::send($sms['mobile'], $sms['msg']);
					\dash\notif::ok("SMS was sended");
				}
			}
			else
			{
				\dash\notif::error(T_("Dont!"));
				return false;
			}
		}

		\dash\redirect::to(\dash\url::this());

	}
}
?>
