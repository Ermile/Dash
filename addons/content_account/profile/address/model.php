<?php
namespace content_account\profile\address;


class model
{

	public static function post()
	{
		$post                = [];
		$post['title']       = \dash\request::post('title');
		$post['firstname']   = \dash\request::post('firstname');
		$post['lastname']    = \dash\request::post('lastname');
		$post['nationality'] = \dash\request::post('nationality');
		$post['city']        = \dash\request::post('city');
		$post['postcode']    = \dash\request::post('postcode');
		$post['phone']       = \dash\request::post('phone');
		$post['fax']         = \dash\request::post('fax');
		$post['address']     = \dash\request::post('address');
		$post['address2']    = \dash\request::post('address2');
		$post['company']     = \dash\request::post('company');
		$post['companyname'] = \dash\request::post('companyname');
		$post['jobtitle']    = \dash\request::post('jobtitle');
		$post['status']      = \dash\request::post('status');

		$result = \dash\app\address::add($post);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Address successfully added"));
			\dash\redirect::pwd();
		}
	}
}
?>