<?php

class cronjob
{
	public function _curl($_requests)
	{
		$handle   = curl_init();
		curl_setopt($handle, CURLOPT_URL, $_requests['url']);
		// curl_setopt($handle, CURLOPT_HTTPHEADER, json_encode($_requests['header'], JSON_UNESCAPED_UNICODE));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true);

		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($_requests['header']));
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($handle, CURLOPT_TIMEOUT, 20);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		$response = curl_exec($handle);
		$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		echo "$mycode \n";
		curl_close ($handle);
	}


	/**
	 * { function_description }
	 */
	public function requests()
	{
		$file = __DIR__. '/list.crontab.txt';
		$list = [];

		if(is_file($file))
		{
			$list = file_get_contents($file);
			$list = json_decode($list, true);
			if(!is_array($list))
			{
				$list = [];
			}
		}

		$token         = time(). '_Ermile_cronjob_'. (string) rand(1,999999). '_'. (string) rand(1,999999). '_'. (string) rand(1,999999);
		$token         = md5($token);
		$list['token'] = $token;

		file_put_contents($file, json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

		$requests   = [];
		foreach ($list as $key => $value)
		{
			if(isset($value['url']))
			{
				$requests[] = ['url' => $value['url'], 'header' => ['token' => $token]];
			}
		}
		return $requests;
	}

	public function run()
	{
		foreach ($this->requests() as $key => $value)
		{
			$this->_curl($value);
		}
	}
}

(new cronjob)->run();

?>