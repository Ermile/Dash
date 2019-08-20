<?php
namespace content_su\smsclient;

class view
{
	public static function config()
	{
		$master_api_key = \dash\option::sms('kavenegar', 'masterkey');
		if(!$master_api_key)
		{
			$master_api_key = \dash\option::sms('kavenegar', 'apikey');
		}

		// send sms
		$api    = new \dash\utility\kavenegar_api($master_api_key);
		$result = $api->client_list();
		if(!is_array($result))
		{
			\dash\notif::error(T_("Unavalible list"));
			return false;
		}

		\dash\data::dataTable($result);

	}
}
?>