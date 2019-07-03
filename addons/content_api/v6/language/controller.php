<?php
namespace content_api\v6\language;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}


		$lang_list = \dash\language::all();
		if(is_array($lang_list))
		{
			foreach ($lang_list as $key => $value)
			{
				$lang_list[$key]['api_url'] = \dash\url::base().'/'. $key .'/api/'. \dash\url::module();
			}
		}

		\content_api\v6::bye($lang_list);
	}
}
?>