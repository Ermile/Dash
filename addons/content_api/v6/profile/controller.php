<?php
namespace content_api\v6\profile;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		\content_api\v6::check_apikey();

		$profile = false;

		if(\dash\request::is('patch'))
		{
			$profile = self::update_profile();
		}
		elseif(\dash\request::is('post'))
		{
			$profile = self::update_profile(true);
		}
		elseif(\dash\request::is('get'))
		{
			$profile = self::get_profile();
		}
		else
		{
			\content_api\v6::no(400);
		}

		\content_api\v6::bye($profile);
	}


	private static function update_profile($_get_avatar = false)
	{

		$request = self::getPost($_get_avatar);
		if(!array_filter($request))
		{
			\dash\notif::error(T_("No field sended"));
			return false;
		}

		// ready request
		$id = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request, $id);

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileAPI', ['code' => \dash\user::id()]);
			\dash\user::refresh();
		}

		return $profile;
	}


	private static function get_profile()
	{
		$detail = \dash\user::detail();
		$result = [];
		foreach ($detail as $key => $value)
		{
			switch ($key)
			{
				case 'username':
				case 'displayname':
				case 'gender':
				case 'title':
				case 'mobile':
				case 'verifymobile':
				case 'status':
				case 'avatar':
				case 'datecreated':
				case 'datemodified':
				case 'birthday':
				case 'language':
				case 'firstname':
				case 'lastname':
				case 'bio':
					$result[$key] = $value;
					break;


				case 'detail':
					$result['email'] = null;
					if($value)
					{
						$myDetail = json_decode($value, true);
						if(isset($myDetail['email']))
						{
							$result['email'] = $myDetail['email'];
						}
					}
					break;
			}
		}

		return $result;
	}




	private static function getPost($_get_avatar = false)
	{
		$post = [];

		// if(array_key_exists('twostep', $_POST))
		// {
		// 	$post['twostep']     = \dash\request::post('twostep');
		// }

		// if(array_key_exists('sidebar', $_POST))
		// {
		// 	$post['sidebar']     = \dash\request::post('sidebar') ? true : false;
		// }

		if(array_key_exists('language', $_POST))
		{
			$post['language']    = \dash\request::post('language');
		}

		if(array_key_exists('website', $_POST))
		{
			$post['website']     = \dash\request::post('website');
		}

		if(array_key_exists('instagram', $_POST))
		{
			$post['instagram']   = \dash\request::post('instagram');
		}

		if(array_key_exists('linkedin', $_POST))
		{
			$post['linkedin']    = \dash\request::post('linkedin');
		}

		if(array_key_exists('facebook', $_POST))
		{
			$post['facebook']    = \dash\request::post('facebook');
		}

		if(array_key_exists('twitter', $_POST))
		{
			$post['twitter']     = \dash\request::post('twitter');
		}

		if(array_key_exists('firstname', $_POST))
		{
			$post['firstname']   = \dash\request::post('firstname');
		}

		if(array_key_exists('lastname', $_POST))
		{
			$post['lastname']    = \dash\request::post('lastname');
		}

		if(array_key_exists('username', $_POST))
		{
			$post['username']    = \dash\request::post('username');
		}

		if(array_key_exists('title', $_POST))
		{
			$post['title']       = \dash\request::post('title');
		}

		if(array_key_exists('bio', $_POST))
		{
			$post['bio']         = \dash\request::post('bio');
		}

		if(array_key_exists('displayname', $_POST))
		{
			$post['displayname'] = \dash\request::post('displayname');
		}

		if(array_key_exists('birthday', $_POST))
		{
			$post['birthday']    = \dash\request::post('birthday');
		}


		if(array_key_exists('gender', $_POST))
		{
			$post['gender']      = \dash\request::post('gender');
		}

		if(array_key_exists('email', $_POST))
		{
			$post['email']       = \dash\request::post('email');
		}

		if($_get_avatar)
		{
			$avatar = \dash\app\file::upload_quick('avatar');

			if($avatar)
			{
				$post['avatar'] = $avatar;
			}
		}

		return $post;
	}


}
?>