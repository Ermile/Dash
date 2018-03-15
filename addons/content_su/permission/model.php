<?php
namespace addons\content_su\permission;


class model extends \addons\content_su\main\model
{
	public function permission_list($_args, $_fields = [])
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;

		if(\lib\utility::get('search'))
		{
			$search = \lib\utility::get('search');
		}
		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}
	}


	/**
	 * Posts an add.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_add($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$id = \lib\utility\shortURL::decode($id);
		if(!$id)
		{
			return false;
		}

		$post = \lib\request::post();
		$perm_list = [];
		foreach ($post as $key => $value)
		{
			if(preg_match("/^perm\_(\d+)$/", $key, $split))
			{
				$perm_list[] = $split[1];
			}
		}

		if(!empty($perm_list))
		{
			$perm_field = implode(',', $perm_list);
			\lib\db\users::update(['permission' => $perm_field], $id);
			\lib\debug::true(T_("Permission added to this user"));
		}

	}
}
?>
