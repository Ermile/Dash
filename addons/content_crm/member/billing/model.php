<?php
namespace content_crm\member\address;


class model
{

	public static function post()
	{
		if(\dash\request::post('btnremove') === 'delete' && \dash\request::post('addressid'))
		{
			\dash\app\address::remove_admin(\dash\request::post('addressid'));
			\dash\redirect::to(\dash\url::this(). '/address?id='. \dash\request::get('id'));
			return;
		}

		$post                = [];
		$post['user_id']     = \dash\coding::decode(\dash\request::get('id'));
		$post['title']       = \dash\request::post('title');
		$post['name']        = \dash\request::post('name');
		$post['country']     = \dash\request::post('country');
		$post['city']        = \dash\request::post('city');
		$post['postcode']    = \dash\request::post('postcode');
		$post['phone']       = \dash\request::post('phone');
		$post['subdomain']   = null;
		$post['province']    = null;
		$post['mobile']      = \dash\request::post('mobile');
		$post['address']     = \dash\request::post('address');
		$post['address2']    = \dash\request::post('address2');
		$post['company']     = \dash\request::post('company');
		$post['companyname'] = \dash\request::post('companyname');
		$post['jobtitle']    = \dash\request::post('jobtitle');


		if(\dash\request::get('addressid'))
		{
			$result = \dash\app\address::edit($post, \dash\request::get('addressid'));
			if(\dash\engine\process::status())
			{
				\dash\notif::ok(T_("Address successfully edited"));
				\dash\redirect::to(\dash\url::this(). '/address?id='. \dash\request::get('id'));
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
