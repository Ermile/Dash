<?php
namespace addons\content_cp\transactions\log;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	public function transactions_log_list($_args, $_fields = [])
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;

		if(!$id)
		{
			return false;
		}

		$meta                   = [];
		$meta['admin']          = true;
		$meta['transaction_id'] = $id;
		$result = \lib\db\transactionlogs::search(null, $meta);
		return $result;
	}
}
?>
