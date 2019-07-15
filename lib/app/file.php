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

		$ready_upload['status'] = null;

		$upload      = \dash\utility\upload::upload($ready_upload);

		if(!\dash\engine\process::status())
		{
			return false;
		}

		$file_id     = null;

		if(isset($upload['id']) && is_numeric($upload['id']))
		{
			$file_id = $upload['id'];
		}
		else
		{
			\dash\notif::error(T_("Can not upload file. undefined error"));
			return false;
		}

		$file_id_code = null;

		if($file_id)
		{
			$file_id_code = \dash\coding::encode($file_id);
		}

		$url = null;

		\dash\log::set('uploadFile', ['code' => $file_id_code, 'datalink' => $url]);

		$result = array_merge($upload, ['code' => $file_id_code]);

		return $result;
	}
}

?>