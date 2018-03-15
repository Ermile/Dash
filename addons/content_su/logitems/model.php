<?php
namespace addons\content_su\logitems;


class model extends \addons\content_su\main\model
{
	public function logitems_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
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

		$result = \lib\db\logitems::search($search, $meta);

		return $result;
	}


	/**
	 * Posts an edit.
	 */
	public function post_edit($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		if(is_numeric($id))
		{
			$update =
			[
				'title'    => \lib\request::post('title'),
				'type'     => \lib\request::post('type'),
				'priority' => \lib\request::post('priority'),
				'desc'     => \lib\request::post('desc'),
			];

			$result = \lib\db\logitems::update($update, $id);
			if($result)
			{
				\lib\debug::true(T_("Update successfull"));
				$this->redirector(\lib\url::here(). '/logitems');
			}
			else
			{
				\lib\debug::error(T_("Update faild"));
			}

		}
	}
}
?>