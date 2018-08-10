<?php
namespace dash\social\telegram;

class log extends tg
{
	public static function save($_method, $_sendData = null, $_sendDate = null, $_response = null)
	{
		$myDetail =
		[
			// 'chatid'        => '',
			'user_id'       => \dash\user::id(),
			'hook'          => self::json(self::$hook),
			// 'hooktext'      => '',
			// 'hookdate'      => '',
			// 'hookmessageid' => '',
			'sendmethod'    => $_method,
			'send'          => self::json($_sendData),
			'senddate'      => $_sendDate,
			// 'sendtext'      => '',
			// 'sendmesageid'  => '',
			// 'sendkeyboard'  => '',
			'response'      => self::json($_response),
			'responsedate'  => date('Y-m-d H:i:s'),
			'url'           => self::$api_token,
			// 'step'          => '',
			// 'meta'          => '',
			// 'status'        => '',
		];

		\dash\db\telegrams::insert($myDetail);
	}

	private static function json($_data)
	{
		if(!$_data)
		{
			return null;
		}
		return json_encode($_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}





	// \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2']);
    // \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2', 'limit' => 1]); // return array by size 1
    // \dash\db\telegrams::insert(['key' => 'value', 'key2' => 'value2']);
    // \dash\db\telegrams::update(['key' => 'value', 'key2' => 'value2'], 10); // where id = 10 update

}
?>