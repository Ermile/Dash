<?php
namespace content_account\my\address;


class controller
{

	public static function routing()
	{
		if(!\dash\option::config('address_page'))
		{
			\dash\header::status(403, T_("This page is locked!"));
		}

		$id = \dash\request::get('addressid');
		if($id)
		{
			$dataRow = \dash\app\address::get($id);
			if(!isset($dataRow['user_id']))
			{
				\dash\header::status(404, T_("Invalid address id"));
			}

			if(intval(\dash\coding::decode($dataRow['user_id'])) !== intval(\dash\user::id()))
			{
				\dash\header::status(403, T_("This is not your address!"));
			}

			\dash\data::dataRowAddress($dataRow);

		}
	}
}
?>