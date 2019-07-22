<?php

class run
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
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($handle, CURLOPT_TIMEOUT, 2);

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
		$cronjob_dir = __DIR__. '/includes/cronjob/';

		if(!is_dir($cronjob_dir))
		{
			@mkdir($cronjob_dir, 0775, true);
		}

		$execlist_file = $cronjob_dir. 'execlist.me.json';

		if(is_file($execlist_file))
		{
			$execlist = file_get_contents($execlist_file);
			$execlist = json_decode($execlist, true);
			if(!is_array($execlist))
			{
				return false;
			}
		}
		else
		{
			return false;
		}

		$token_file = $cronjob_dir .'token.me.json';

		$token_json = [];

		if(is_file($token_file))
		{
			$token_json = file_get_contents($token_file);
			$token_json = json_decode($token_json, true);
			if(!is_array($token_json))
			{
				$token_json = [];
			}
		}

		$token         = time(). '_Ermile_cronjob_'. (string) rand(1,999999). '_'. (string) rand(1,999999). '_'. (string) rand(1,999999);
		$token         = md5($token);

		$token_json['token'] = $token;
		$token_json['date']  = date("Y-m-d H:i:s");

		file_put_contents($token_file, json_encode($token_json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

		$requests   = [];
		foreach ($execlist as $key => $value)
		{
			if(isset($value['url']))
			{
				$requests[] = ['url' => $value['url'], 'header' => ['token' => $token]];
			}
		}
		return $requests;
	}


	public function exec()
	{
		$requests = $this->requests();
		if(is_array($requests))
		{
			foreach ($requests as $key => $value)
			{
				$this->_curl($value);
			}
		}
	}
}

$cronjob = new \run;
$cronjob->exec();


?>