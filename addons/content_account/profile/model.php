<?php
namespace content_account\profile;


class model extends \content_account\main\model
{
	public function post_profile($_args)
	{

		// update user details
		$update_user = [];

		// check the user is login
		if(!\lib\user::login())
		{
			\lib\notif::error(T_("Please login to change your profile"), false, 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\request::post('name')) > 50)
		{
			\lib\notif::error(T_("Please enter your name less than 50 character"), 'name', 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\request::post('displayname')) > 50)
		{
			\lib\notif::error(T_("Please enter your displayname less than 50 character"), 'displayname', 'arguments');
			return false;
		}

		// check name lenght
		if(mb_strlen(\lib\request::post('family')) > 50)
		{
			\lib\notif::error(T_("Please enter your family less than 50 character"), 'family', 'arguments');
			return false;
		}

		$file_code = null;
		$temp_url  = null;

		if(\lib\request::files('avatar'))
		{
			$this->user_id = \lib\user::id();
			\lib\utility::set_request_array(['upload_name' => 'avatar']);
			$uploaded_file = $this->upload_file(['\lib\notif' => false]);

			if(isset($uploaded_file['url']))
			{
				$temp_url                = $uploaded_file['url'];
				$host                    = \lib\url::site(). '/';
				$temp_url                = str_replace($host, '', $temp_url);
				$update_user['avatar']  = $temp_url;
			}
			// if in upload have error return
			if(!\lib\notif::$status)
			{
				return false;
			}
		}


		// if the postion exist update user display postion
		if(\lib\request::post('displayname') !== \lib\user::login('displayname'))
		{
			$update_user['displayname'] = \lib\request::post('displayname');
		}


		// update user record
		if(!empty($update_user))
		{
			\lib\db\users::update($update_user, \lib\user::id());
			\lib\user::refresh();
		}

		if(\lib\notif::$status)
		{
			\lib\notif::ok(T_("Profile data was updated"));
			\lib\notif::direct();
			\lib\redirect::to(\lib\url::here());
		}
	}

}
?>