<?php
namespace dash\db\mysql\tools;

trait log
{
	/**
	 * save log of sql request into file for debug
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	public static function log($_text, $_time = null, $_name = 'log.sql', $_type = 'sql')
	{
		$classes  = (array_column(debug_backtrace(), 'file'));

		// start saving
		$fileAddr = root.'/includes/log/database/';
		$time_ms  = round($_time*1000);
		$date_now = new \DateTime("now", new \DateTimeZone('Asia/Tehran') );
		\dash\file::makeDir($fileAddr, null, true);
		// set file address
		$fileAddr .= $_name;
		$my_text = "\n#". $date_now->format("Y-m-d H:i:s");
		$my_text .= " @". \dash\user::id();
		if($_time)
		{
			// $my_text .= "--- ". $_time. " s";
			$my_text .= " --- ". $time_ms . " ms";
		}
		$my_text .= " | ". str_repeat("-", 5). ' '. \dash\url::pwd();
		//$my_text .= "\n---". mysqli_info(self::$link);
		// $my_text .= "\n";
		if($time_ms > 1000)
		{
			$my_text .= "\n"."--- CRITICAL!";
		}
		elseif($time_ms > 500)
		{
			$my_text .= "\n"."--- WARN!";
		}
		elseif($time_ms > 200)
		{
			$my_text .= "\n"."--- CHECK!";
		}
		// switch for special type of text
		switch ($_type)
		{
			case 'sql':
				if(strlen($_text) > 250)
				{
					// simplify this query in multi line
					$_text = str_replace("\t\t\t", "\t", $_text);
					$_text = str_replace('   ', ' ', $_text);
				}
				else
				{
					$_text = trim($_text);
					$_text = preg_replace('!\s+!', ' ', $_text);
				}
				// add tab before it
				// trim input text
				$_text = trim($_text);
				$_text =  $_text;
				break;
			case 'json' :
				if(is_array($_text) || is_object($_text))
				{
					$_text = json_encode($_text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				}
			break;
		}
		// add final text
		$my_text .= "\n";
		$my_text .= $_text;
		$my_text .= "\r";

		@file_put_contents($fileAddr, $my_text, FILE_APPEND);

		// add to start of file
		// $fileContent = '';
		// if(file_exists($fileAddr))
		// {
		// 	$fileContent = file_get_contents ($fileAddr);
		// }
		// file_put_contents ($fileAddr, $my_text . "\n" . $fileContent);
	}
}
?>
