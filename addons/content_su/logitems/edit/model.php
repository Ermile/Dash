<?php
namespace content_su\logitems\edit;


class model
{
	/**
	 * Posts an edit.
	 */
	public static function post()
	{
		$id = \dash\request::get('id');
		if(is_numeric($id))
		{
			$update =
			[
				'title'    => \dash\request::post('title'),
				'type'     => \dash\request::post('type'),
				'priority' => \dash\request::post('priority'),
				'desc'     => \dash\request::post('desc'),
			];

			$result = \dash\db\logitems::update($update, $id);
			if($result)
			{
				\dash\notif::ok(T_("Update successfull"));
				\dash\redirect::to(\dash\url::here(). '/logitems');
			}
			else
			{
				\dash\notif::error(T_("Update faild"));
			}

		}
	}
}
?>