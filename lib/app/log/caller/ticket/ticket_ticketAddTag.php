<?php
namespace dash\app\log\caller\ticket;

class ticket_ticketAddTag
{
	public static function site($_args = [])
	{
		$tag = isset($_args['data']['tag']) ? $_args['data']['tag']: null;
		$title = '';
		$title .= T_("Add tag to ticket");
		if($tag)
		{
			$title .= " ". $tag;
		}

		return $title;
	}
}
?>