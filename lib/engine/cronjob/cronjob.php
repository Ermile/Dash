<?php
require_once('backup.php');

class cronjob
{
	public function run()
	{
		$dir = __DIR__;
		$dir = str_replace('dash/lib/engine/cronjob', '', $dir);
		$dir .= 'cronjob.php';

		if(is_file($dir))
		{
			exec("php $dir");
		}
	}
}

(new cronjob)->run();

(new backup)->run();
?>