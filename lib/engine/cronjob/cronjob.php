<?php
require_once('cronjob_log.php');
require_once('backup.php');

class cronjob
{
	public function run()
	{
		$dir = __DIR__;
		$dir = str_replace('dash/lib/engine/cronjob', '', $dir);
		$dir .= 'cronjob.php';

		cronjob_log::save($dir);
		if(is_file($dir))
		{
			cronjob_log::save('Try to php '. $dir. ' ...');
			exec("php $dir");
		}
	}
}

(new cronjob)->run();

(new backup)->run();
?>