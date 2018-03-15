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
		$this->user_id = $this->login('id');
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
			'firstname'            => \lib\utility::post('name'),
			'lastname'             => \lib\utility::post('lastName'),
			'mobile'               => \lib\utility::post('mobile'),
			'nationalcode'         => \lib\utility::post('nationalcode'),
			'father'               => \lib\utility::post('father'),
			'birthday'             => \lib\utility::post('birthday'),
			'gender'               => \lib\utility::post('gender'),
			'marital'              => \lib\utility::post('marital'),
			'child'                => \lib\utility::post('child'),
			'birthcity'            => \lib\utility::post('birthcity'),
			'shfrom'               => \lib\utility::post('shfrom'),
			'shcode'               => \lib\utility::post('shcode'),
			'education'            => \lib\utility::post('education'),
			'job'                  => \lib\utility::post('job'),
			'passportcode'         => \lib\utility::post('passportcode'),
			'passportexpire'       => \lib\utility::post('passportexpire'),
			'paymentaccountnumber' => \lib\utility::post('paymentaccountnumber'),
			'shaba'                => \lib\utility::post('shaba'),
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
		$this->user_id = $this->login('id');
		$request['id'] = $id;

		\lib\utility::set_request_array($request);
		// API ADD MEMBER FUNCTION
		$this->add_user(['method' => 'patch']);

		if(\lib\debug::$status)
		{
			$this->redirector(\lib\url::pwd());
		}
	}
}
?>
