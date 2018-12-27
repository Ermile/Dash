<?php
namespace content_crm\member\avatar;


class model
{

	/**
	 * UploAads an avatar.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function upload_avatar()
	{
		if(\dash\request::files('avatar'))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => 'avatar']);

			if(isset($uploaded_file['url']))
			{
				return $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\dash\engine\process::status())
			{
				return false;
			}
		}
		return null;
	}



	/**
	 * Posts an addmember.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function post()
	{
		$request           = [];
		if(\dash\request::post('btn') === 'remove')
		{
			$request['avatar'] = null;
		}
		else
		{

			$file_url     = self::upload_avatar();

			// we have an error in upload avatar
			if($file_url === false)
			{
				return false;
			}

			if($file_url === null)
			{
				\dash\notif::warn(T_("To change the image, please re-open the new file"),'avatar');
				return false;
			}

			$request['avatar'] = $file_url;
		}

		\dash\db\users::update($request, \dash\coding::decode(\dash\request::get('id')));

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("User successfully updated"));
			\dash\redirect::pwd();
		}
	}
}
?>