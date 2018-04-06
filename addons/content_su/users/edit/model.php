<?php
namespace addons\content_su\users\edit;


class model extends \addons\content_su\main\model
{
	public function getUserDetail($_args)
	{
		$id            = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$request       = [];
		$request['id'] = $id;

		\dash\utility::set_request_array($request);
		$this->user_id = \dash\user::id();
		return $this->get_user();
	}


	/**
	 * Gets the post teacher.
	 *
	 * @return     array  The post teacher.
	 */
	public function getPostUser()
	{
		$post =
		[
			'firstname'            => \dash\request::post('name'),
			'lastname'             => \dash\request::post('lastName'),
			'mobile'               => \dash\request::post('mobile'),
			'nationalcode'         => \dash\request::post('nationalcode'),
			'father'               => \dash\request::post('father'),
			'birthday'             => \dash\request::post('birthday'),
			'gender'               => \dash\request::post('gender'),
			'marital'              => \dash\request::post('marital'),
			'child'                => \dash\request::post('child'),
			'birthcity'            => \dash\request::post('birthcity'),
			'shfrom'               => \dash\request::post('shfrom'),
			'shcode'               => \dash\request::post('shcode'),
			'education'            => \dash\request::post('education'),
			'job'                  => \dash\request::post('job'),
			'passportcode'         => \dash\request::post('passportcode'),
			'passportexpire'       => \dash\request::post('passportexpire'),
			'paymentaccountnumber' => \dash\request::post('paymentaccountnumber'),
			'shaba'                => \dash\request::post('shaba'),
		];
		// $file_code = $this->upload_avatar();
		// // we have an error in upload avatar
		// if($file_code === false)
		// {
		// 	return false;
		// }
		// if($file_code)
		// {
		// 	$post['file'] = $file_code;
		// }
		return $post;
	}


	/**
	 * Posts a teacher add.
	 */
	public function post_edit($_args)
	{
		// ready request
		$request = $this->getPostUser();
		if($request === false)
		{
			return false;
		}

		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$this->user_id = \dash\user::id();
		$request['id'] = $id;

		\dash\utility::set_request_array($request);
		// API ADD MEMBER FUNCTION
		$this->add_user(['method' => 'patch']);

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
