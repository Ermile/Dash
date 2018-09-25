<?php
namespace dash\log;

trait send
{

	// send notification
	public static function send($_caller, $_user_id = null, $_replace = [], $_option = [])
	{
		self::clean();

		$all_list = self::lists();

		if(!isset($all_list[$_caller]))
		{
			return null;
		}

		self::$notif_detail = $notif_detail = $all_list[$_caller];

		$user_detail = self::detect_user($_user_id);

		if($user_detail === false)
		{
			return false;
		}

		if(!is_array($_replace))
		{
			$_replace = [];
		}

		if(!is_array($_option))
		{
			$_option = [];
		}

		$add_notif = [];

		if(isset($_option['user_idsender']) && is_numeric($_option['user_idsender']))
		{
			$add_notif['user_idsender'] = $_option['user_idsender'];
		}

		if(isset($_option['meta']) && is_string($_option['meta']))
		{
			$add_notif['meta'] = $_option['meta'];
		}

		if(\dash\url::subdomain())
		{
			$add_notif['subdomain'] = \dash\url::subdomain();
		}

		$status = self::detail('status');
		if(!$status)
		{
			$status = 'enable';
		}

		$add_notif['caller'] = $_caller;
		$add_notif['notif']  = 1;
		$add_notif['status'] = $status;

		$save = self::save_notification_record($add_notif);
		return $save;
	}


	private static function save_notification_record($_detail)
	{
		$user_detail = self::$all_user_detail;

		$add_notif      = [];

		foreach ($user_detail as $key => $value)
		{
			$temp_add_notif      = $_detail;

			if(!isset($value['id']))
			{
				continue;
			}

			$temp_add_notif['user_id'] = $value['id'];

			$add_notif[] = $temp_add_notif;
		}

		if(!empty($add_notif))
		{
			$result = \dash\db\logs::multi_insert($add_notif);
		}

		return false;
	}

}
?>
