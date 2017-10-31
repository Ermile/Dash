<?php
namespace content_account\profile;
use \lib\debug;
use \lib\utility;

class model extends \content_account\main\model
{
	/**
	 * Posts a setup 2.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_profile($_args)
	{

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'session' => $_SESSION,
			],
		];

		// update user details
		$update_user = [];
		$user_session = [];

		// check the user is login
		if(!$this->login())
		{
			debug::error(T_("Please login to change your profile"), false, 'arguments');
			return false;
		}


		// check name lenght
		if(mb_strlen(utility::post('name')) > 50)
		{
			debug::error(T_("Please enter your name less than 50 character"), 'name', 'arguments');
			return false;
		}


		// check name lenght
		if(mb_strlen(utility::post('displayname')) > 50)
		{
			debug::error(T_("Please enter your displayname less than 50 character"), 'displayname', 'arguments');
			return false;
		}


		// check name lenght
		if(mb_strlen(utility::post('family')) > 50)
		{
			debug::error(T_("Please enter your family less than 50 character"), 'family', 'arguments');
			return false;
		}

		$file_code = null;
		$temp_url  = null;
		if(utility::files('avatar'))
		{
			$this->user_id = $this->login('id');
			utility::set_request_array(['upload_name' => 'avatar']);
			$uploaded_file = $this->upload_file(['debug' => false]);

			if(isset($uploaded_file['url']))
			{
				$temp_url                = $uploaded_file['url'];
				$host                    = Protocol."://" . \lib\router::get_root_domain(). '/';
				$temp_url                = str_replace($host, '', $temp_url);
				$update_user['avatar']  = $temp_url;
				$user_session['avatar'] = $temp_url;
			}
			// if in upload have error return
			if(!debug::$status)
			{
				return false;
			}
		}


		// if the postion exist update user display postion
		if(utility::post('displayname') != $this->login('displayname'))
		{
			$update_user['displayname'] = utility::post('displayname');
			$user_session['displayname'] = $update_user['displayname'];
		}


		// update user record
		if(!empty($update_user))
		{

			\lib\db\users::update($update_user, $this->login('id'));
			if(isset($_SESSION['user']) && is_array($_SESSION['user']))
			{
				$_SESSION['user'] = array_merge($_SESSION['user'], $user_session);
			}
		}

		if(debug::$status)
		{
			debug::true(T_("Profile data was updated"));
			debug::msg('direct', true);
			$this->redirector()->set_domain()->set_url('account');
		}
	}

}
?>