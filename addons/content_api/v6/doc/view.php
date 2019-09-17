<?php
namespace content_api\v6\doc;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::page_title( T_(':val API documentation v6', ['val' => \dash\data::site_title()]));
		\dash\data::page_desc(T_('Last modified'). ' '. \dash\datetime::fit('2019-02-21 17:30', 'human', 'year'));
		\dash\data::page_pictogram('campfire');

		\dash\data::apiURL(\dash\url::site(). '/'. \dash\language::current(). '/api/v6/');


		self::dash_doc();

		self::load_project_api_doc();
	}

	private static function dash_doc()
	{
		$dashAPIDoc = [];
		$addr       = __DIR__;

		if(is_dir($addr))
		{
			$list = glob($addr . '/*');

			if($list && is_array($list))
			{
				foreach ($list as $key => $value)
				{
					if(in_array(basename($value), ['display.html', 'view.php']))
					{
						continue;
					}
					else
					{
						$dashAPIDoc[] = str_replace(addons, '', $value);
					}
				}
			}
		}

		\dash\data::dashAPIDoc($dashAPIDoc);

	}


	private static function load_project_api_doc()
	{
		$projectDoc     = [];
		$projectSidebar = null;

		$addr           = root. 'content_api/v6/doc';

		if(is_dir($addr))
		{
			$list = glob($addr . '/*');
			if($list && is_array($list))
			{
				foreach ($list as $key => $value)
				{
					if(basename($value) === 'sidebar.html')
					{
						$projectSidebar = str_replace(root, '', $value);
					}
					else
					{
						$projectDoc[] = str_replace(root, '', $value);
					}
				}
			}
		}


		\dash\data::projectDoc($projectDoc);
		\dash\data::projectSidebar($projectSidebar);
	}
}
?>