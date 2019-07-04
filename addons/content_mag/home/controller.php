<?php
namespace content_mag\home;

class controller
{
	public static function routing()
	{
		\dash\data::isMag(false);

		$module = \dash\url::module();

		$check_arg =
		[
			'type'   => 'mag',
			'url'   => urldecode(\dash\url::directory()),
			'limit'  => 1
		];


		if(\dash\permission::check('cpMagEditForOthers'))
		{
			$check_arg['status']   = ["NOT IN", "('deleted')"];
		}
		else
		{
			$check_arg['status'] = 'publish';
		}


		$check = \dash\db\posts::get($check_arg);
		if($check)
		{
			\dash\data::isMag(true);
			\dash\data::moduelRow($check);
			\dash\open::get();
		}

	}
}
?>