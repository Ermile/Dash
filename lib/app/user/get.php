<?php
namespace dash\app\user;


trait get
{
	public static function get($_id)
	{
		$_id = \dash\coding::decode($_id);
		if(!$_id)
		{
			return false;
		}

		$result = \dash\db\users::get_by_id($_id);

		if(is_array($result))
		{
			return self::ready($result);
		}

		return $result;
	}
}
?>