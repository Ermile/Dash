<?php
namespace content_account\profile\signature;


class model
{




	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$request              = [];
		$request['signature'] = \dash\request::post('signature') ? $_POST['signature'] : null;

		$request['id']        = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request);

		if(\dash\engine\process::status())
		{
			\dash\log::db('editProfileSignatur');
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>