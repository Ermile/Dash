<?php
namespace content_api\v6\notif;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		\content_api\v6::check_apikey();

		$notif = self::notif();

		\content_api\v6::bye($notif);
	}


	private static function notif()
	{
		$notif     = [];

		$user_id = \dash\user::id();

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
		// in this version needless to send read method
		if(\dash\request::post('read') || true)
		{
			if(is_array($dataTable))
			{
				$readdate = array_column($dataTable, 'readdate');
				if(count(array_filter($readdate)) !== count($readdate))
				{
					\dash\app\log::set_readdate($dataTable_raw, true, $user_id);
				}
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
					case "id":
						$new[$index][$key] = \dash\coding::encode($value);

						break;
					case "readdate":
					case "title":
					case "excerpt":
					case "text":
					case "icon":
					case "cat":

					case "datecreated":
					case "image":
					case "footer":
					case "url":
						$new[$index][$key] = $value;
						break;
					case 'data':
						if(is_array($value))
						{
							foreach ($value as $k => $v)
							{
								switch ($k)
								{
									case 'notif_title':
									case 'notif_small':
									case 'notif_big':
									case 'notif_sub_text':
									case 'notif_group':
									case 'notif_large_icon':
									case 'notif_icon':
									case 'notif_link':
									case 'notif_external':
										$new[$index][substr($k, 6)] = $v;
									break;
								}
							}
						}
						break;

					case "send":
					case "from":
					case "caller":
					case "id_raw":
					case "status":
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
					default:
						// $new[$key] = $value;
						break;
				}
			}
		}

		return $new;
	}
}
?>