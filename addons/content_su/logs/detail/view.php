<?php
namespace content_su\logs\detail;

class view
{
	public static function config()
	{
		\dash\data::badge_link(\dash\url::here(). '/logs');
		\dash\data::badge_text(T_('Back to Logs list'));

		$id = \dash\request::get('id');

		if($id && is_numeric($id))
		{
			$result = \dash\db\logs::get(['id' => $id, 'limit' => 1]);
			\dash\data::logDetail($result);
		}
	}
}
?>