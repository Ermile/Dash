<?php
namespace addons\content_cp\users\edit;
use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\main\model
{
	public function getUserDetail($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$request                = [];
		$request['id'] = $id;

		utility::set_request_array($request);
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
			'firstname'            => utility::post('name'),
			'lastname'             => utility::post('lastName'),
			'mobile'               => utility::post('mobile'),
			'nationalcode'         => utility::post('nationalcode'),
			'father'               => utility::post('father'),
			'birthday'             => utility::post('birthday'),
			'gender'               => utility::post('gender'),
			'marital'              => utility::post('marital'),
			'child'                => utility::post('child'),
			'brithcity'            => utility::post('brithcity'),
			'shfrom'               => utility::post('shfrom'),
			'shcode'               => utility::post('shcode'),
			'education'            => utility::post('education'),
			'job'                  => utility::post('job'),
			'passportcode'         => utility::post('passportcode'),
			'passportexpire'       => utility::post('passportexpire'),
			'paymentaccountnumber' => utility::post('paymentaccountnumber'),
			'shaba'                => utility::post('shaba'),
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

		utility::set_request_array($request);
		// API ADD MEMBER FUNCTION
		$this->add_user(['method' => 'patch']);

		if(debug::$status)
		{
			$this->redirector($this->url('full'));
		}
	}


}
?>
