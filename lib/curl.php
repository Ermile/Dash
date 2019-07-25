<?php
namespace dash;

class curl
{
	public static function go($_url, $_data = null, $_dataType = null, $_header = null, $_fullResponse = false)
	{
		$ch       = curl_init();
		$customHeader = [];
		curl_setopt($ch, CURLOPT_URL, $_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// set timeout to connect
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);

		if($_data)
		{
			$postFields = null;
			// fill based on input data
			switch ($_dataType)
			{
				case 'json':
					$postFields = json_encode($_data, JSON_UNESCAPED_UNICODE);
					// add json to haeder
					array_push($customHeader, 'Content-Type: application/json');
					break;

				case 'post':
					$postFields = http_build_query($_data);
					curl_setopt($ch, CURLOPT_POST, true);
					break;

				case 'form-data':
				default:
					// simple get
					// do nothing
					break;
			}

			// set postFields if exist
			if($postFields)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			}
		}

		// combine custom headers
		if(is_array($_header))
		{
			// merge 2 array
			$customHeader = array_merge($customHeader, $_header);
			// remove duplicates
			$customHeader = array_unique($customHeader);
		}
		if(is_array($customHeader) && count($customHeader) > 0 )
		{
			// set cusotm header
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);
		}

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		// execute curl and get response
		$curlResult  = curl_exec($ch);

		if($curlResult === false)
		{
			// we have error
			$finalResult = curl_error($ch);
		}
		else
		{
			$finalResult = $curlResult;
		}

		// get first char of result
		$resultFirstChar = substr($finalResult, 0, 1);
		if($finalResult && ($resultFirstChar === "{" || $resultFirstChar === "["))
		{
			$finalResult = json_decode($finalResult, true);
		}

		if($_fullResponse)
		{
			$finalResult =
			[
				'response' => $finalResult,
				'code'     => curl_getinfo($ch, CURLINFO_HTTP_CODE),
				'header'   => curl_getinfo($ch, CURLINFO_HEADER_OUT),
				'info'     => curl_getinfo($ch),
			];
		}
		// close connection
		curl_close($ch);

		// return final result
		return $finalResult;
	}
}
?>
