<?php
namespace content_support\home;

class controller
{
	public static function routing()
	{
		\dash\data::isHelpCenter(false);

		$module = \dash\url::module();
		if(in_array($module, ['ticket']))
		{

		}
		else
		{
			$check = \dash\db\posts::get(['type' => 'help', 'slug' => $module, 'parent' => null, 'status' => 'publish', 'limit' => 1]);
			if($check)
			{
				\dash\data::isHelpCenter(true);
				\dash\data::moduelRow($check);
				\dash\open::get();


			}
		}
	}
}
?>
