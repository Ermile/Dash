<?php
namespace content_account\my\avatar;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Edit your avatar'));
		\dash\data::page_desc(T_('You can edit your avatar.'));

		\content_account\my\view::load_me();
	}
}
?>
