<?php
namespace content_account\address;


class controller
{

	public static function routing()
	{
		$id = \dash\request::get('id');
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

			\dash\data::dataRow($dataRow);

		}
	}
}
?>