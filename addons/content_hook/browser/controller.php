<?php
namespace content_hook\browser;

class controller
{
	public static function routing()
	{
		if(\dash\url::child() === null)
		{
			\dash\code::pretty(\dash\utility\browserDetection::browser_detection('full_assoc'));
			\dash\code::boom();
		}

	}
}
?>