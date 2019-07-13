<?php
namespace content_cms\posts\home;


class controller
{
	public static function routing()
	{
		$myType = \dash\request::get('type');

		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					\dash\permission::access('cpPageView');
					break;

				case 'help':
					\dash\permission::access('cpHelpCenterView');
					break;

				case 'post':
					\dash\permission::access('cpPostsView');
					break;

				default:
					$allowPostType = \dash\option::config('allow_post_type');
					if($allowPostType && is_array($allowPostType))
					{
						if(in_array($myType, $allowPostType))
						{
							// no problem
						}
						else
						{
							\dash\header::status(404);
						}
					}
					else
					{
						\dash\header::status(404);
					}
					break;
			}
		}
		else
		{
			\dash\permission::access('cpPostsView');
		}


	}
}
?>