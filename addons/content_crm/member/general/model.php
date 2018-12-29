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
			'title'       => \dash\request::post('title'),
			'bio'         => \dash\request::post('bio'),
			'displayname' => \dash\request::post('displayname'),

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


		\dash\app\user::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>