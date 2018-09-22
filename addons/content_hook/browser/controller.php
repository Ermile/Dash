<?php
namespace content_hook\browser;

class controller
{
	public static function routing()
	{
		if(\dash\url::child() === null)
		{
			echo "<pre>";
			print_r(\dash\utility\browserDetection::browser_detection('full_assoc'));
			echo "</pre>";
			exit();
		}

	}
}
?>