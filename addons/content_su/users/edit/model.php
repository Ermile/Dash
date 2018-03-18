<?php
namespace addons\content_su\users\edit;


class model extends \addons\content_su\main\model
{
	public function getUserDetail($_args)
	{
		$id            = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$request       = [];
		$request['id'] = $id;

		\lib\utility::set_request_array($request);
		$this->user_id = \lib\user::id();
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
			'firstname'            => \lib\request::post('name'),
			'lastname'             => \lib\request::post('lastName'),
			'mobile'               => \lib\request::post('mobile'),
			'nationalcode'         => \lib\request::post('nationalcode'),
			'father'               => \lib\request::post('father'),
			'birthday'             => \lib\request::post('birthday'),
			'gender'               => \lib\request::post('gender'),
			'marital'              => \lib\request::post('marital'),
			'child'                => \lib\request::post('child'),
			'birthcity'            => \lib\request::post('birthcity'),
			'shfrom'               => \lib\request::post('shfrom'),
			'shcode'               => \lib\request::post('shcode'),
			'education'            => \lib\request::post('education'),
			'job'                  => \lib\request::post('job'),
			'passportcode'         => \lib\request::post('passportcode'),
			'passportexpire'       => \lib\request::post('passportexpire'),
			'paymentaccountnumber' => \lib\request::post('paymentaccountnumber'),
			'shaba'                => \lib\request::post('shaba'),
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
		$this->user_id = \lib\user::id();
		$request['id'] = $id;

		\lib\utility::set_request_array($request);
		// API ADD MEMBER FUNCTION
		$this->add_user(['method' => 'patch']);

		if(\lib\engine\process::status())
		{
			\lib\redirect::pwd();
		}
	}
}
?>
