<?php
namespace content_cms\posts\add;

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
					\dash\permission::access('cpPageAdd');
					break;

				case 'help':
					\dash\permission::access('cpHelpCenterAdd');
					break;

				case 'post':
					\dash\permission::access('cpPostsAdd');
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
			\dash\permission::access('cpPostsAdd');
		}
	}
}
?>