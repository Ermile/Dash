<?php
namespace content_su\smsclient\home;

class model
{
	public static function post()
	{
		$apikey = \dash\request::post('apikey');
		if(!$apikey)
		{
			\dash\notif::error(T_("Api key not set"));
			return false;
		}

		$result = null;

		$master_api_key = \dash\option::sms('kavenegar', 'masterkey');
		if(!$master_api_key)
		{
			$master_api_key = \dash\option::sms('kavenegar', 'apikey');
		}

		$api    = new \dash\utility\kavenegar_api($master_api_key);

		switch (\dash\request::post('type'))
		{
			case 'empty_credit':

				$client_detail = $api->client_fetch(['apikey' => $apikey]);

				if(isset($client_detail['remaincredit']) && $client_detail['remaincredit'] && is_numeric($client_detail['remaincredit']))
				{
					$result = $api->client_chargecredit(['apikey' => $apikey, 'credit' => intval($client_detail['remaincredit']) * -1]);
				}
				else
				{
					\dash\notif::error(T_("This client credit is zero!"));
					return false;
				}
				break;

			case 'setstatus':
				$result = $api->client_setstatus(['apikey' => $apikey, 'status' => \dash\request::post('status')]);
				break;

			default:
				\dash\notif::error(T_("Dont!"));
				return false;
				break;
		}

		// send sms
		if(!is_array($result))
		{
			\dash\notif::error(T_("Unknown error"));
			return false;
		}

		if(isset($api->msg))
		{
			\dash\notif::info($api->msg);
		}

		\dash\redirect::pwd();

	}
}
?>