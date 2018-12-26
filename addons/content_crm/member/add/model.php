<?php
namespace content_crm\member\add;


class model
{

	public static function getPost()
	{
		$post =
		[
			'firstname'   => \dash\request::post('name'),
			'lastname'    => \dash\request::post('lastName'),
			'father'      => \dash\request::post('father'),
			'birthdate'   => \dash\request::post('birthdate'),
			'gender'      => \dash\request::post('gender'),
			'shcode'      => \dash\request::post('shcode'),
			'mobile'      => \dash\request::post('mobile'),
			'nationality' => \dash\request::post('nationality'),
			'status'      => 'active',

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

		return $post;
	}


	/**
	 * Posts a member add.
	 */
	public static function post()
	{
		// ready request
		$request = self::getPost();

		if($request === false)
		{
			return false;
		}

		if(!$request['firstname'] && !$request['lastname'])
		{
			\dash\notif::error(T_("Fill name is require!"), 'name');
			return false;

			\dash\notif::error(T_("Fill first or lastname is require!"), 'lastname');
			return false;
		}

		$result = \dash\app\member::add($request);

		if(\dash\engine\process::status())
		{
			if(isset($result['member_id']))
			{
				\dash\redirect::to(\dash\url::here(). '/member/general?id='. $result['member_id']);
			}
			else
			{
				\dash\redirect::to(\dash\url::here(). '/member');
			}
		}
	}
}
?>