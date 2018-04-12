<?php
namespace content_su\logitems\edit;

class view
{
	public static function config()
	{
		$id = \dash\request::get('id');
		if($id && is_numeric($id))
		{
			$result = \dash\db\logitems::get(['id' => $id, 'limit' => 1]);
			\dash\data::logitem($result);
		}
	}
}
?>