<?php
namespace content_su\smsclient\edit;

class view
{
	public static function config()
	{
		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_("Back"));

		$master_api_key = \dash\option::sms('kavenegar', 'masterkey');
		if(!$master_api_key)
		{
			$master_api_key = \dash\option::sms('kavenegar', 'apikey');
		}

		$apikey = \dash\request::get('apikey');
		if(!$apikey)
		{
			\dash\header::status(404);
		}

		// send sms
		$api    = new \dash\utility\kavenegar_api($master_api_key);
		$result = $api->client_fetch(['apikey' => $apikey]);

		\dash\data::dataRow($result);

		if(isset($api->msg))
		{
			\dash\notif::warn($api->msg);
		}

	}
}
?>