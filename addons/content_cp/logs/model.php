<?php
namespace addons\content_cp\logs;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	public function logs_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \lib\db\logs::search($search, $meta);

		return $result;
	}
}
?>
