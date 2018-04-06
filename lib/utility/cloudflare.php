<?php
namespace dash\utility;
/**
 * Class for cloudflare.
 * api to cloudflare
 * https://api.cloudflare.com/
 */
class cloudflare
{

	private static $api_url = 'https://api.cloudflare.com/client/v4/';
	private static $header  = [];
	private static $method  = 'GET';


	/**
	 * Creates a dns record.
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function create_dns_record($_args)
	{
		$default_args =
		[
			// A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF
			'type'    => null,
			// dns name
			// example.com
			'name'    => null,
			// dns record content
			// 127.0.0.1
			'content' => null,
			// time to live for dns record
			// value of 1 is automatic
			'ttl'     => 1,
			//Whether the record is receiving the performance and security benefits of Cloudflare
			'proxied' => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$run           = [];
		$run['method'] = 'POST';
		$run['url']    = "zones/:zone_identifier/dns_records";
		$run['data']   = $_args;

		return self::_curl($run);
	}


	/**
	 * get list of dns record
	 * https://api.cloudflare.com/
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function list_dns_record($_args = [])
	{
		$default_args =
		[
			// A, AAAA, CNAME, TXT, SRV, LOC, MX, NS, SPF
			'type' => null,
			// dns name
			// example.com
			'name' => null,
			// dns record content
			// 127.0.0.1
			'content' => null,

		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$run           = [];
		$run['method'] = 'GET';
		$_args = array_filter($_args);

		$string_query = [];
		foreach ($_args as $key => $value)
		{
			$string_query[] = "$key=$value";
		}

		if(is_array($string_query) && $string_query)
		{
			$string_query = implode('&', $string_query);
		}
		else
		{
			$string_query = null;
		}

		// $run['url']    = "zones/:zone_identifier/dns_records?". $string_query;
		$run['url']    = "zones/:zone_identifier/dns_records";

		$result = self::_curl($run);
		return $result;
	}



	/**
	 * config api to connect to cloudflare
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private static function config($_args)
	{
		$status                  = \dash\option::config('cloudflare', 'status');
		if(!$status) 			 return false;

		$ZoneID                  = \dash\option::config('cloudflare', 'ZoneID');
		if(!$ZoneID) 			 return false;

		$X_Auth_Key              = \dash\option::config('cloudflare', 'X-Auth-Key');
		if(!$X_Auth_Key)		 return false;

		$X_Auth_Email            = \dash\option::config('cloudflare', 'X-Auth-Email');
		if(!$X_Auth_Email)		 return false;

		$X_Auth_User_Service_Key = \dash\option::config('cloudflare', 'X-Auth-User-Service-Key');

		if(!$X_Auth_Email && !$X_Auth_User_Service_Key && !$X_Auth_Key)
		{
			return false;
		}

		self::$header[] = "Content-Type: application/json";
		self::$header[] = "X-Auth-Key: $X_Auth_Key";
		self::$header[] = "X-Auth-Email: $X_Auth_Email";

		if($X_Auth_User_Service_Key)
		{
			self::$header[] = "X-Auth-User-Service-Key: $X_Auth_User_Service_Key ";
		}

		if(isset($_args['method']))
		{
			self::$method = mb_strtolower($_args['method']);
		}


		if(isset($_args['url']))
		{

			$api_url = self::$api_url;
			$api_url .= $_args['url'];

			if(strpos($_args['url'], ':zone_identifier') !== false)
			{
				$api_url = str_replace(':zone_identifier', $ZoneID, $api_url);
			}
			self::$api_url = $api_url;
		}
	}


	/**
	 * run curl to connect to cloudflare
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function _curl($_args)
	{
		self::config($_args);

		$handle   = curl_init();
		curl_setopt($handle, CURLOPT_URL, self::$api_url);
		curl_setopt($handle, CURLOPT_HTTPHEADER, self::$header);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

		if(self::$method === 'PUT')
		{
			curl_setopt($handle, CURLOPT_PUT, true);
		}
		elseif(self::$method === 'POST')
		{
			curl_setopt($handle, CURLOPT_POST, true);
		}
		else
		{
			// DEFAULT METHOD IS GET
		}

		if(isset($_args['data']))
		{
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($_args['data'], JSON_UNESCAPED_UNICODE));
		}
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($handle, CURLOPT_TIMEOUT, 3);

		if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4'))
		{
 			curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}

		$response = curl_exec($handle);
		$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close ($handle);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'url'      => self::$api_url,
				'header'   => self::$header,
				'data'     => $_args,
				'response' => $response,
			],
		];
		\dash\db\logs::set('cloudflare_curl:exec', null, $log_meta);

		if(intval($mycode) === 200)
		{
			\dash\notif::ok(T_("The api Cloudflare opration success"));
		}
		else
		{
			\dash\notif::warn(T_("The api Cloudflare opration faild"));
		}
		return $response;
	}
}
?>
