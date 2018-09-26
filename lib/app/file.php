<?php
namespace dash\app;


class file
{

	public static function upload_quick($_upload_name)
	{
		if(\dash\request::files($_upload_name))
		{
			$uploaded_file = self::upload(['debug' => false, 'upload_name' => $_upload_name]);

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


	public static function upload($_options = [])
	{
		\dash\app::variable($_options);

		$default_options =
		[
			'upload_name' => \dash\app::request('upload_name'),
			'url'         => null,
			'debug'       => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(\dash\app::request('url') && !$_options['url'])
		{
			$_options['url'] = \dash\app::request('url');
		}

		$file_path = false;

		if($_options['url'])
		{
			$file_path = true;
		}
		elseif(!\dash\request::files($_options['upload_name']))
		{
			\dash\notif::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
			return false;
		}

		$ready_upload            = [];
		$ready_upload['user_id'] = \dash\user::id();
		$ready_upload['debug']   = $_options['debug'];

		if($file_path)
		{
			$ready_upload['file_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		$ready_upload['status'] = 'publish';

		// $ready_upload['user_size_remaining'] = self::remaining(\dash\user::id());

		// upload::$extentions = ['png', 'jpeg', 'jpg'];

		$upload      = \dash\utility\upload::upload($ready_upload);

		if(!\dash\engine\process::status())
		{
			return false;
		}

		$file_detail = \dash\temp::get('upload');
		$file_id     = null;

		// if(isset($file_detail['size']))
		// {
		// 	self::user_size_plus(\dash\user::id(), $file_detail['size']);
		// }

		if(isset($file_detail['id']) && is_numeric($file_detail['id']))
		{
			$file_id = $file_detail['id'];
		}
		else
		{
			return \dash\notif::error(T_("Can not upload file. undefined error"));
		}

		$file_id_code = null;

		if($file_id)
		{
			$file_id_code = \dash\coding::encode($file_id);
		}

		$url = null;

		if(isset($file_detail['url']))
		{
			if(\dash\option::config('upload_subdomain'))
			{
				$url  = '';
				$url .= \dash\url::protocol(). '://';
				$url .= \dash\option::config('upload_subdomain'). '.';
				$url .= \dash\url::domain(). '/';
				$url .= $file_detail['url'];
			}
			else
			{
				$url = \dash\url::site(). '/'. $file_detail['url'];
			}
		}

		\dash\log::set('uploadFile', ['data' => $file_id_code, 'datalink' => $url]);

		return ['code' => $file_id_code, 'url' => $url];
	}
}

?>