<?php
namespace addons\content_su\users\detail;


class model extends \addons\content_su\main\model
{
	public function get_load($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$id = \dash\coding::decode($id);
		$result = [];
		if($id && is_numeric($id))
		{
			$result = \dash\db\users::get_by_id($id);
		}
		return $result;
	}
}
?>
