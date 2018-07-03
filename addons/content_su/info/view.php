<?php
namespace content_su\info;

class view
{
	public static function config()
	{
		$result                            = [];
		$result['max_execution_time']      = ini_get('max_execution_time');
		$result['max_file_uploads']        = ini_get('max_file_uploads');
		$result['max_input_time']          = ini_get('max_input_time');
		$result['max_input_vars']          = ini_get('max_input_vars');
		$result['memory_limit']            = ini_get('memory_limit');
		$result['soap.wsdl_cache_enabled'] = ini_get('soap.wsdl_cache_enabled');
		$result['curl.cainfo']             = ini_get('curl.cainfo');
		// gettext
		// soap
		// zip
		// curl
		// mb string
		\dash\data::phpIniInfo($result);

	}
}
?>