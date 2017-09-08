<?php
namespace addons\content_api\v1\user\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\upload;

trait add
{


	public function upload_user($_options = [])
	{
		debug::title(T_("Can not upload user"));

		$default_options =
		[
			'upload_name' => utility::request('upload_name'),
			'url'         => null,
			'debug'       => true,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		if(utility::request('url') && !$_options['url'])
		{
			$_options['url'] = utility::request('url');
		}

		$user_path = false;

		if($_options['url'])
		{
			$user_path = true;
		}
		elseif(!utility::users($_options['upload_name']))
		{
			return debug::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
		}

		$ready_upload            = [];
		$ready_upload['user_id'] = $this->user_id;
		$ready_upload['debug']   = $_options['debug'];

		if($user_path)
		{
			$ready_upload['user_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		// if(\lib\permission::access('admin:admin:view', null, $this->user_id))
		// {
		// 	$ready_upload['post_status'] = 'publish';
		// }
		// else
		// {
		// 	$ready_upload['post_status'] = 'draft';
		// }

		$ready_upload['post_status'] = 'publish';

		$ready_upload['user_size_remaining'] = self::remaining($this->user_id);

		upload::$extentions = ['png', 'jpeg', 'jpg'];

		$upload      = upload::upload($ready_upload);

		if(!debug::$status)
		{
			return false;
		}

		$user_detail = \lib\storage::get_upload();
		$user_id     = null;

		if(isset($user_detail['size']))
		{
			self::user_size_plus($this->user_id, $user_detail['size']);
		}

		if(isset($user_detail['id']) && is_numeric($user_detail['id']))
		{
			$user_id = $user_detail['id'];
		}
		else
		{
			return debug::error(T_("Can not upload user. undefined error"));
		}

		$user_id_code = null;

		if($user_id)
		{
			$user_id_code = utility\shortURL::encode($user_id);
		}

		$url = null;

		if(isset($user_detail['url']))
		{
			$url = Protocol."://" . \lib\router::get_root_domain() . '/'. $user_detail['url'];
		}

		debug::title(T_("user upload completed"));
		return ['code' => $user_id_code, 'url' => $url];
	}
}

?>