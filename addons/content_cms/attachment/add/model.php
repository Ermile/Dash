<?php
namespace content_cms\attachment\add;


class model
{
	public static function post()
	{
		\dash\permission::access('cpPostsAdd');

		if(\dash\request::files('gallery'))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => 'gallery']);

			if(!\dash\engine\process::status())
			{
				\dash\notif::error(T_("Can not upload file"));
			}
			else
			{
				\dash\notif::ok(T_("File successfully uploaded"));
			}
		}

		if(\dash\engine\process::status())
		{
			\dash\notif::direct();
			\dash\redirect::to(\dash\url::this());
		}
	}
}
?>
