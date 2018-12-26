<?php
namespace content_crm\member\description;


class model
{
	public static function post()
	{
		\dash\permission::access('aMemberEdit');

		$request           = [];
		$request['desc'] = \dash\request::post('desc');

		\dash\app\member::edit($request, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>