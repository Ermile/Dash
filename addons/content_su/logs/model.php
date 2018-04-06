<?php
namespace addons\content_su\logs;


class model extends \addons\content_su\main\model
{
	public function logs_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \dash\db\logs::search($search, $meta);

		return $result;
	}
}
?>
