<?php
namespace content_crm\member\address;


class controller
{
	public static function routing()
	{
		\dash\permission::access('cpUsersEdit');
		$id = \dash\request::get('addressid');
		if($id)
		{
			$dataRow = \dash\app\address::get($id);
			if(!isset($dataRow['user_id']))
			{
				\dash\header::status(404, T_("Invalid address id"));
			}

			\dash\data::dataRowAddress($dataRow);

		}
	}
}
?>