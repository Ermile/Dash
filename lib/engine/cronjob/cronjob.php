<?php
require_once('cronjob_log.php');
require_once('backup.php');

class cronjob
{
	public function run()
	{
		$dir = __DIR__. '/masterurl.me.txt';
		if(is_file($dir))
		{
			$this->_curl(file_get_contents($dir));
		}

		// $dir = str_replace('dash/lib/engine/cronjob', '', $dir);
		// $dir .= 'cronjob.php';

		// // cronjob_log::save($dir);
		// if(is_file($dir))
		// {
		// 	cronjob_log::save(date("Y-m-d H:i:s"). ' --- Try to php '. $dir. ' ...');
		// 	exec("php $dir");
		// }
	}

	public function _curl($_masterurl)
	{
		$token         = time(). '_Ermile_cronjob_'. (string) rand() . (string) rand();
		$token         = md5($token);

		$token_json['token'] = $token;
		$token_json['date']  = date("Y-m-d H:i:s");

		file_put_contents(__DIR__. '/token.me.json', json_encode($token_json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

		$handle   = curl_init();
		curl_setopt($handle, CURLOPT_URL, $_masterurl);
		// curl_setopt($handle, CURLOPT_HTTPHEADER, json_encode($_requests['header'], JSON_UNESCAPED_UNICODE));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true);

		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($token_json));
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($handle, CURLOPT_TIMEOUT, 30);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		$response = curl_exec($handle);
		$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close ($handle);
		echo $_masterurl;
		echo '|';
		echo $mycode;
		echo '|';
		var_dump($response);

	}
}

(new cronjob)->run();

(new backup)->run();
?>