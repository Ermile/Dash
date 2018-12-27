<?php
namespace content_crm\member\general;


class model
{

	public static function post()
	{
		$post =
		[
			'mobile'      => \dash\request::post('mobile'),
			'firstname'   => \dash\request::post('name'),
			'lastname'    => \dash\request::post('lastName'),
			'father'      => \dash\request::post('father'),
			'marital'     => \dash\request::post('marital'),
			'birthdate'   => \dash\request::post('birthdate'),
			'gender'      => \dash\request::post('gender'),
			'shcode'      => \dash\request::post('shcode'),
			'nationality' => \dash\request::post('nationality'),
			'permission'  => \dash\request::post('permission') == '0' ? null : \dash\request::post('permission'),
			// 'status'      => \dash\request::post('status'),
		];

		if(\dash\request::post('nationality') === 'IR')
		{
			$post['nationalcode'] = \dash\request::post('nationalcode');
		}
		else
		{
			$post['pasportdate'] = \dash\request::post('passportdate');
			$post['pasportcode'] = \dash\request::post('pasportcode');
		}

		if(intval(\dash\coding::decode(\dash\request::get('id'))) === \dash\user::id())
		{
			if(isset($post['permission']) && $post['permission'] !== 'admin' && \dash\user::detail('permission') === 'admin' )
			{
				\dash\notif::warn(T_("You can not set your permission less than admin!"));
				$post['permission'] = 'admin';
			}
		}

		\dash\app\member::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>