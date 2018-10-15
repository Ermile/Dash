<?php
namespace content_account\address;


class model
{

	public static function post()
	{
		if(\dash\request::post('btnremove') === 'delete' && \dash\request::post('id'))
		{
			\dash\app\address::remove(\dash\request::post('id'));
			\dash\redirect::to(\dash\url::this());
			return;
		}

		$post                = [];
		$post['title']       = \dash\request::post('title');
		$post['firstname']   = \dash\request::post('firstname');
		$post['lastname']    = \dash\request::post('lastname');
		$post['country']     = \dash\request::post('country');
		$post['city']        = \dash\request::post('city');
		$post['postcode']    = \dash\request::post('postcode');
		$post['phone']       = \dash\request::post('phone');
		$post['subdomain']   = null;
		$post['province']    = null;
		$post['fax']         = \dash\request::post('fax');
		$post['address']     = \dash\request::post('address');
		$post['address2']    = \dash\request::post('address2');
		$post['company']     = \dash\request::post('company');
		$post['companyname'] = \dash\request::post('companyname');
		$post['jobtitle']    = \dash\request::post('jobtitle');

		if(\dash\request::get('id'))
		{
			$result = \dash\app\address::edit($post, \dash\request::get('id'));
			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("Address successfully edited"));
				\dash\redirect::to(\dash\url::this());
			}
		}
		else
		{
			$result = \dash\app\address::add($post);
			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("Address successfully added"));
				\dash\redirect::pwd();
			}
		}

	}
}
?>