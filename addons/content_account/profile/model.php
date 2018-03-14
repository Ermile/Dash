<?php
namespace content_account\profile;


class model extends \content_account\main\model
{
	public function post_profile($_args)
	{

		// update user details
		$update_user = [];

		// check the user is login
		if(!$this->login())
		{
			\lib\debug::error(T_("Please login to change your profile"), false, 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\utility::post('name')) > 50)
		{
			\lib\debug::error(T_("Please enter your name less than 50 character"), 'name', 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\utility::post('displayname')) > 50)
		{
			\lib\debug::error(T_("Please enter your displayname less than 50 character"), 'displayname', 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\utility::post('family')) > 50)
		{
			\lib\debug::error(T_("Please enter your family less than 50 character"), 'family', 'arguments');
			return false;
		}

		$file_code = null;
		$temp_url  = null;

		if(\lib\utility::files('avatar'))
		{
			$this->user_id = $this->login('id');
			\lib\utility::set_request_array(['upload_name' => 'avatar']);
			$uploaded_file = $this->upload_file(['\lib\debug' => false]);

			if(isset($uploaded_file['url']))
			{
				$temp_url                = $uploaded_file['url'];
				$host                    = \lib\url::protocol()."://" . \lib\router::get_root_domain(). '/';
				$temp_url                = str_replace($host, '', $temp_url);
				$update_user['avatar']  = $temp_url;
			}
			// if in upload have error return
			if(!\lib\debug::$status)
			{
				return false;
			}
		}


		// if the postion exist update user display postion
		if(\lib\utility::post('displayname') !== $this->login('displayname'))
		{
			$update_user['displayname'] = \lib\utility::post('displayname');
		}


		// update user record
		if(!empty($update_user))
		{
			\lib\db\users::update($update_user, $this->login('id'));
			\lib\user::refresh();
		}

		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("Profile data was updated"));
			\lib\debug::msg('direct', true);
			$this->redirector(\lib\url::here());
		}
	}

}
?>