<?php
namespace dash\app;


class file
{

	public static function upload($_options = [])
	{
		\lib\app::variable($_options);

		$default_options =
		[
			'upload_name' => \lib\app::request('upload_name'),
			'url'         => null,
			'debug'       => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(\lib\app::request('url') && !$_options['url'])
		{
			$_options['url'] = \lib\app::request('url');
		}

		$file_path = false;

		if($_options['url'])
		{
			$file_path = true;
		}
		elseif(!\lib\request::files($_options['upload_name']))
		{
			\lib\notif::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
			return false;
		}

		$ready_upload            = [];
		$ready_upload['user_id'] = \lib\user::id();
		$ready_upload['debug']   = $_options['debug'];

		if($file_path)
		{
			$ready_upload['file_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		// if(\lib\permission::access('admin:admin:view', null, \lib\user::id()))
		// {
		// 	$ready_upload['status'] = 'publish';
		// }
		// else
		// {
		// 	$ready_upload['status'] = 'draft';
		// }

		$ready_upload['status'] = 'publish';

		// $ready_upload['user_size_remaining'] = self::remaining(\lib\user::id());

		// upload::$extentions = ['png', 'jpeg', 'jpg'];

		$upload      = \lib\utility\upload::upload($ready_upload);

		if(!\lib\engine\process::status())
		{
			return false;
		}

		$file_detail = \lib\temp::get('upload');
		$file_id     = null;

		// if(isset($file_detail['size']))
		// {
		// 	self::user_size_plus(\lib\user::id(), $file_detail['size']);
		// }

		if(isset($file_detail['id']) && is_numeric($file_detail['id']))
		{
			$file_id = $file_detail['id'];
		}
		else
		{
			return \lib\notif::error(T_("Can not upload file. undefined error"));
		}

		$file_id_code = null;

		if($file_id)
		{
			$file_id_code = \lib\coding::encode($file_id);
		}

		$url = null;

		if(isset($file_detail['url']))
		{
			$url = \lib\url::site(). '/'. $file_detail['url'];
		}

		return ['code' => $file_id_code, 'url' => $url];
	}
}

?>