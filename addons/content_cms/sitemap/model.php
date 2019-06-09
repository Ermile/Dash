<?php
namespace content_cms\sitemap;

class model
{
	public static function post()
	{
		if(\dash\request::post('run') === 'yes')
		{
			$result = \dash\utility\sitemap::create();
			\dash\session::set('result_create_sitemap' , $result);
			\dash\notif::ok(T_("Sitemap successfully created"));
			\dash\redirect::pwd();
			return;
		}


		if(\dash\request::post('remove') === 'remove')
		{
			$count = 0;
			$dir = \dash\utility\sitemap::folder_addr();
			if(is_dir($dir))
			{
				$files = glob($dir. '*');
				if(is_array($files))
				{
					foreach ($files as $key => $value)
					{
						\dash\file::delete($value);
						$count++;
					}
				}

			}

			$file = \dash\utility\sitemap::file_addr();
			if(is_file($file))
			{
				\dash\file::delete($file);
				$count++;
			}

			\dash\session::set('result_create_sitemap' , null);
			\dash\notif::ok(\dash\utility\human::fitNumber($count). ' '. T_("File removed"));
			\dash\redirect::pwd();

		}
	}
}
?>