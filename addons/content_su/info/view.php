<?php
namespace content_su\info;

class view
{
	public static function config()
	{
		$result                            = [];
		$result['mbstring']                = extension_loaded('mbstring');
		$result['soap']                    = class_exists("soapclient");
		$result['curl']                    = function_exists('curl_version');
		$result['zip']                     = class_exists("ZipArchive");
		$result['max_execution_time']      = ini_get('max_execution_time');
		$result['upload_max_filesize']     = ini_get('upload_max_filesize');
		$result['post_max_size']           = ini_get('post_max_size');
		$result['max_file_uploads']        = ini_get('max_file_uploads');
		$result['max_input_time']          = ini_get('max_input_time');
		$result['max_input_vars']          = ini_get('max_input_vars');
		$result['memory_limit']            = ini_get('memory_limit');
		$result['soap.wsdl_cache_enabled'] = ini_get('soap.wsdl_cache_enabled');
		$result['curl.cainfo']             = ini_get('curl.cainfo');
		$result['display_errors']          = ini_get('display_errors');
		$result['display_startup_errors']  = ini_get('display_startup_errors');
		$result['error_reporting']         = ini_get('error_reporting');
		$result['log_errors']              = ini_get('log_errors');
		$result['session.name']            = ini_get('session.name');


		\dash\log::set('loadServerInfo');


		\dash\data::phpIniInfo($result);

	}
}
?>