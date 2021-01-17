<?php
namespace dash;



class json
{

	public static function decode($_json, $_array = true)
	{
		if(!is_string($_json))
		{
			return [];
		}

		$_json = preg_replace('/\r|\n/','\n', trim($_json));

		$array = json_decode($_json, $_array);

		if(!is_array($array))
		{
			$array = [];
		}

		return $array;
	}


	public static function encode($_array)
	{
		if(!is_array($_array) || !is_object($_array))
		{
			return null;
		}

		$json = json_encode($_array, JSON_UNESCAPED_UNICODE);

		return $json;
	}

}
?>