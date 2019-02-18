<?php
namespace dash\app\log\caller\notif;


class notif_text
{
	public static function site($_args = [])
	{

		$result               = [];

		$result["title"]      = isset($_args['data']['notif_title']) ? $_args['data']['notif_title'] : null;
		$result["small"]      = isset($_args['data']['notif_small']) ? $_args['data']['notif_small'] : null;
		$result["big"]        = isset($_args['data']['notif_big']) ? $_args['data']['notif_big'] : null;
		$result["sub_text"]   = isset($_args['data']['notif_sub_text']) ? $_args['data']['notif_sub_text'] : null;
		$result["group"]      = isset($_args['data']['notif_group']) ? $_args['data']['notif_group'] : null;
		$result["sender"]     = isset($_args['data']['notif_sender']) ? $_args['data']['notif_sender'] : null;
		$result["large_icon"] = isset($_args['data']['notif_large_icon']) ? $_args['data']['notif_large_icon'] : null;
		$result["icon"]       = isset($_args['data']['notif_icon']) ? $_args['data']['notif_icon'] : null;
		$result["link"]       = isset($_args['data']['notif_link']) ? $_args['data']['notif_link'] : null;
		$result["external"]   = isset($_args['data']['notif_external']) ? $_args['data']['notif_external'] : null;

		$result['excerpt']    = $result['small'];
		$result['cat']        = $result['group'];
		$result['iconClass']  = 'fc-blue';
		$result['txt']        = $result['big'];

		return $result;

	}

	public static function expire()
	{
		return date("Y-m-d H:i:s", strtotime("+365 days")); // 1 year
	}

	public static function is_notif()
	{
		return true;
	}

}
?>