<?php
namespace content_crm\permission\add;


class model
{
	public static function post()
	{
		$update = true;
		$name   = \dash\request::get('id');
		if(!$name)
		{
			$update = false;
			$name   = \dash\request::post('name');
		}

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

		$save = \dash\permission::save_permission($name, $label, $contain, $update);
		if($save)
		{
			\dash\log::set('permissionUpdate', ['name' => $name, 'label' => $label]);
			if($update)
			{
				\dash\redirect::pwd();
			}
			else
			{
				\dash\redirect::to(\dash\url::this());
			}
		}

	}
}
?>