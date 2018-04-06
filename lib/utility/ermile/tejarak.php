<?php
namespace dash\utility\ermile;

class tejarak
{
	private $api_url = "https://tejarak.com/api/%s/%s";
	private $version = null;
	private $url     = null;
	private $data    = [];
	private $header  = [];
	private $result  = [];

	/**
	 * ready to connect to tejarak api
	 *
	 * @param      string  $_token    The token
	 * @param      string  $_version  The version
	 * @param      array   $_header   The header
	 */
	public function __construct($_mobile, $_token = null, $_version = null, $_header = [])
	{
		if(\dash\url::isLocal())
		{
			$this->api_url = "http://tejarak.local/api/%s/%s";
		}

		if(!\dash\option::config('tejarak', 'status'))
		{
			return false;
		}

		if($_token === null)
		{
			$_token = \dash\option::config('tejarak', 'token');
		}

		if($_version === null)
		{
			$_version = 1;
		}

		$this->version = 'v'. $_version;

		array_push($this->header, "Accept: application/json");
		array_push($this->header, "Content-type: application/json");
		array_push($this->header, "Authorization: $_token");
		array_push($this->header, "mobile: $_mobile");

		if(is_array($_header) && !empty($_header))
		{
			foreach ($_header as $key => $value)
			{
				array_push($this->header, $value);
			}
		}
	}


	/**
	 * connect to tejarak api
	 */
	private function response()
	{
		$raw_url = $this->api_url;

		$this->api_url = sprintf($this->api_url, $this->version, $this->url);

		$handle   = curl_init();

		curl_setopt($handle, CURLOPT_URL, $this->api_url);

		$this->api_url = $raw_url;

		curl_setopt($handle, CURLOPT_HTTPHEADER, $this->header);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

		if(\dash\url::isLocal() === false)
		{
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 1);
		}

		if($this->method === 'put')
		{
			curl_setopt($handle, CURLOPT_PUT, true);
		}
		elseif($this->method === 'post')
		{
			curl_setopt($handle, CURLOPT_POST, true);
		}
		else
		{
			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, mb_strtoupper($this->method));
		}

		if(!empty($this->data))
		{
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($this->data, JSON_UNESCAPED_UNICODE));
		}

		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($handle, CURLOPT_TIMEOUT, 2);

		$response = curl_exec($handle);
		curl_close ($handle);
		$response = json_decode($response, true);
		return $response;
	}


	/**
	 * tejarak api function
	 *
	 * @param      <type>   $_url   The url
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function __call($_url, $_args)
	{
		if(substr($_url, 0, 4) === 'get_')
		{
			$key = substr($_url, 4);

			if($key === 'result')
			{
				if(isset($this->result['result']))
				{
					return $this->result['result'];
				}
			}

			if(isset($this->result['result'][$key]))
			{
				return $this->result['result'][$key];
			}
			return false;
		}

		$this->url = $_url;
		if(!isset($_args[0]))
		{
			return false;
		}

		$method = mb_strtolower($_args[0]);
		if(!in_array($method, ['post', 'put', 'patch', 'get', 'delete']))
		{
			return false;
		}

		$this->method = $method;

		if(isset($_args[1]))
		{
			$this->data = $_args[1];
		}

		if($method === 'get' && is_array($this->data) && !empty($this->data))
		{
			$this->url .= '?'. http_build_query($this->data);
		}
		$this->result = $this->response();
		return $this->handle($this->result);
	}


	/**
	 * handle result
	 *
	 * @param      <type>  $_result  The result
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function handle($_result)
	{
		return $_result;
	}
}
?>
