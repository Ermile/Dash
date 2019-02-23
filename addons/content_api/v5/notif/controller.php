<?php
namespace content_api\v5\notif;


class controller
{
	public static function routing()
	{
		\content_api\v5::check_authorization3_v5();

		$notif = self::notif();

		\content_api\v5::end5($notif);
	}


	private static function notif()
	{
		$notif     = [];

		$usercode = \dash\header::get('usercode');
		if(!$usercode)
		{
			return false;
		}

		$user_id = \dash\coding::decode($usercode);

		if(!$user_id)
		{
			return false;
		}


		$args =
		[
			'sort'  => 'logs.id',
			'order' => 'desc',
			'to'    => $user_id,
		];

		$args['logs.status'] = "notif";

		$search_string   = null;

		$dataTable_raw = $dataTable = \dash\app\log::list($search_string, $args);

		$dataTable = self::ready_api($dataTable);

		if(is_array($dataTable))
		{
			$readdate = array_column($dataTable, 'readdate');
			if(count(array_filter($readdate)) !== count($readdate))
			{
				\dash\app\log::set_readdate($dataTable_raw, true, $user_id);
			}
		}

		return $dataTable;
	}

	private static function ready_api($_data)
	{
		if(!$_data || !is_array($_data))
		{
			return false;
		}

		$new = [];

		foreach ($_data as $index => $notif)
		{
			foreach ($notif as $key => $value)
			{
				switch ($key)
				{
					case "readdate":
					case "title":
					case "icon":
					case "cat":
					case "iconClass":
					case "api_title":
					case "excerpt":
					case "text":
					case "subdomain":
					case "code":
					case "to":
					case "notif":
					default:
						$new[$index][$key] = $value;
						break;

					case "data":
					case "send":
					case "from":
					case "caller":
					case "id":
					case "id_raw":
					case "status":
					case "datecreated":
					case "datemodified":
					case "visitor_id":
					case "meta":
					case "ip":
					case "sms":
					case "telegram":
					case "email":
					case "expiredate":
					case "displayname":
					case "mobile":
					case "avatar":
					case 'caller':
						continue;

						// $new[$key] = $value;
						break;
				}
			}
		}

		return $new;
	}
}
?>