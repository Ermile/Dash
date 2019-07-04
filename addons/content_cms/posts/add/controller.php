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

				case 'mag':
					if(!\dash\option::config('mag'))
					{
						\dash\header::status(403, T_("The magazine manager for this system is not enable"));
					}
					\dash\permission::access('cpMagAdd');
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