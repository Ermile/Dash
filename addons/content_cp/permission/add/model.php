<?php
namespace content_cp\permission\add;


class model
{
	public static function post()
	{

		$name   = \dash\request::post('name');
		$label  = \dash\request::post('label');

		$contain = [];

		foreach (\dash\request::post() as $key => $value)
		{
			if($key == 'name' || $key == 'label')
			{
				continue;
			}
			if($value)
			{
				$contain[] = $key;
			}
		}

		$save = \dash\permission::save_permission($name, $label, $contain);
		if($save)
		{
			\dash\redirect::to(\dash\url::this());
		}

	}
}
?>