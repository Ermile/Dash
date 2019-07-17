<?php
namespace dash;

class curl
{
	public static function go($_url, $_header = null, $_json = true)
	{
		$handle   = curl_init();
		curl_setopt($handle, CURLOPT_URL, $_url);
		// curl_setopt($handle, CURLOPT_HTTPHEADER, json_encode($_header, JSON_UNESCAPED_UNICODE));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		if($_header)
		{
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($_header));
		}
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($handle, CURLOPT_TIMEOUT, 20);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		$response = curl_exec($handle);
		$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		curl_close ($handle);

		if($response && $_json === true)
		{
			$response = json_decode($response, true);
		}
		var_dump(55);
		var_dump($mycode);
		var_dump($response);
		return $response;
	}
}
?>
