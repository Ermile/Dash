<?php
class cronjob_log
{
	public static function save($_data)
	{
		file_put_contents(__DIR__. '/cronjob.me.log', $_data. PHP_EOL, FILE_APPEND);
	}
}
?>