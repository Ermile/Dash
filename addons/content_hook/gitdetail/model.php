<?php
namespace content_hook\gitdetail;


class model
{

	public static function post()
	{
		$token = \dash\request::post('token');
		if(!$token)
		{
			return false;
		}

		if(self::check_token($token))
		{
			\dash\log::set('su_CentralizedGitUpdate');

			// dash update
			$dashLocation = null;
			// check dash location
			if(is_dir(root. 'dash'))
			{
				$dashLocation = '../dash';
			}
			elseif(is_dir(root. '../dash'))
			{
				$dashLocation = '../../dash';
			}
			\dash\utility\git::pull($dashLocation);
			\dash\utility\git::pull(root, false);

			\dash\db::$link_open    = [];
			\dash\db::$link_default = null;

			if(defined('db_user') && defined('db_pass'))
			{
				\dash\db::$db_user = constant("db_user");
				\dash\db::$db_pass = constant("db_pass");

				\dash\db::$debug_error = false;

				// db upgrade
				\dash\db::install(true, true);
			}
		}
	}


	private static function check_token($_token)
	{
		$url            = 'http://ermile.local/git';

		$field               = [];
		$field['checktoken'] = true;
		$field['token']      = $_token;
		$field['project']    = \dash\url::domain();


		$handle         = curl_init();

		curl_setopt($handle, CURLOPT_URL, $url);
		// curl_setopt($handle, CURLOPT_HTTPHEADER, json_encode($_requests['header'], JSON_UNESCAPED_UNICODE));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true);

		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($field));
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($handle, CURLOPT_TIMEOUT, 2);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		$response = curl_exec($handle);

		curl_close ($handle);

		$response = json_decode($response, true);

		if(isset($response['ok']) && $response['ok'])
		{
			return true;
		}
		return false;
	}

}
?>