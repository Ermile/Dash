<?php
namespace content_account\my\signature;


class model
{
	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$request              = [];
		$request['signature'] = \dash\request::post('signature') ? $_POST['signature'] : null;

		$id  = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request, $id);

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileSignatur', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>