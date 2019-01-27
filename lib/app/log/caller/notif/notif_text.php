<?php
namespace dash\app\log\caller\notif;


class notif_text
{
	public static function site($_args = [])
	{
		$text      = isset($_args['data']['mytext']) ? $_args['data']['mytext'] : null;
		$title     = isset($_args['data']['mytitle']) ? $_args['data']['mytitle'] : T_("Notification");
		$cat       = isset($_args['data']['mycat']) ? $_args['data']['mycat'] : T_("Notification");
		$iconClass = isset($_args['data']['iconClass']) ? $_args['data']['iconClass'] : 'fc-green';
		$icon      = isset($_args['data']['icon']) ? $_args['data']['icon'] : 'bullhorn';
		// $excerpt   = isset($_args['data']['myexcerpt']) ? $_args['data']['myexcerpt'] : 'bullhorn';

		$result              = [];
		$result['title']     = $title;
		$result['icon']      = $icon;
		$result['cat']       = $cat;
		$result['iconClass'] = $iconClass;
		// $result['excerpt']   = $excerpt;
		$result['txt']       = $text;

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