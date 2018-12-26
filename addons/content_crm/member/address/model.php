<?php
namespace content_crm\member\address;


class model
{

	public static function post()
	{
		\dash\permission::access('aMemberEdit');
		$post             = [];
		$post['country']  = \dash\request::post('country');
		// $post['province'] = \dash\request::post('province');
		$post['city']     = \dash\request::post('city');
		$post['zipcode']  = \dash\request::post('zipcode');
		$post['address'] = \dash\request::post('address');

		\dash\app\member::edit($post,\dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
