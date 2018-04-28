<?php
namespace content_account\view;


class model
{
	public static function post()
	{
		if(!\dash\user::id())
		{
			return;
		}

		$togglesidebar = \dash\request::post('togglesidebar') ? true : false;

		$meta = \dash\user::detail('meta');
		$new_meta = [];
		if(is_string($meta) && substr($meta, 0, 1) === '{')
		{
			$new_meta = json_decode($meta, true);
		}
		elseif(is_array($meta))
		{
			$new_meta = $meta;
		}

		$new_meta['toggle_sidebar'] = $togglesidebar;

		$new_meta = json_encode($new_meta, JSON_UNESCAPED_UNICODE);

		\dash\db\users::update(['meta' => $new_meta], \dash\user::id());

		\dash\user::refresh();

		\dash\notif::ok(T_("Your change was saved"));

		\dash\notif::direct();

		\dash\redirect::pwd();
	}
}
?>