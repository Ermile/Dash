<?php
namespace content_api\v1\file\tools;


trait link
{
	use check;

	public function upload_file($_options = [])
	{
		// \dash\notif::title(T_("Can not upload file"));

		$default_options =
		[
			'upload_name' => \dash\utility::request('upload_name'),
			'url'         => null,
			'debug'       => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(\dash\utility::request('url') && !$_options['url'])
		{
			$_options['url'] = \dash\utility::request('url');
		}

		$file_path = false;

		if($_options['url'])
		{
			$file_path = true;
		}
		elseif(!\dash\request::files($_options['upload_name']))
		{
			return \dash\notif::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
		}

		$ready_upload            = [];
		$ready_upload['user_id'] = $this->user_id;
		$ready_upload['debug']   = $_options['debug'];

		if($file_path)
		{
			$ready_upload['file_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		// if(\dash\permission::access('admin:admin:view', null, $this->user_id))
		// {
		// 	$ready_upload['status'] = 'publish';
		// }
		// else
		// {
		// 	$ready_upload['status'] = 'draft';
		// }

		$ready_upload['status'] = 'publish';

		$ready_upload['user_size_remaining'] = self::remaining($this->user_id);

		// upload::$extentions = ['png', 'jpeg', 'jpg'];

		$upload      = \dash\utility\upload::upload($ready_upload);

		if(!\dash\engine\process::status())
		{
			return false;
		}

		$file_detail = \dash\temp::get('upload');
		$file_id     = null;

		if(isset($file_detail['size']))
		{
			self::user_size_plus($this->user_id, $file_detail['size']);
		}

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
			$url = \dash\url::site(). '/'. $file_detail['url'];
		}

		// \dash\notif::title(T_("File upload completed"));
		return ['code' => $file_id_code, 'url' => $url];
	}
}

?>