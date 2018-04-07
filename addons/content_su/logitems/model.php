<?php
namespace content_su\logitems;


class model extends \addons\content_su\main\model
{
	public function logitems_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \dash\db\logitems::search($search, $meta);

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
				'title'    => \dash\request::post('title'),
				'type'     => \dash\request::post('type'),
				'priority' => \dash\request::post('priority'),
				'desc'     => \dash\request::post('desc'),
			];

			$result = \dash\db\logitems::update($update, $id);
			if($result)
			{
				\dash\notif::ok(T_("Update successfull"));
				\dash\redirect::to(\dash\url::here(). '/logitems');
			}
			else
			{
				\dash\notif::error(T_("Update faild"));
			}

		}
	}
}
?>