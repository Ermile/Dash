<?php
namespace content_api\v6\posts;


class controller
{
	public static function routing()
	{
		if(\dash\url::subchild())
		{
			\content_api\v6::no(404);
		}

		if(!\dash\request::is('get'))
		{
			\content_api\v6::no(400);
		}


		$detail = self::posts();

		\content_api\v6::bye($detail);
	}



	private static function posts()
	{
		$args = [];

		$limit = \dash\request::get('limit');

		if($limit && is_numeric($limit) && intval($limit) > 1 && intval($limit) < 100)
		{
			$args['limit'] = intval($limit);
		}

		$posts  = \dash\app\posts::get_post_list($args);
		$result = [];
		if(is_array($posts))
		{
			foreach ($posts as $index => $myPost)
			{
				foreach ($myPost as $field => $value)
				{
					switch ($field)
					{
						case 'id':
						case 'language':
						case 'title':
						case 'seotitle':
						case 'slug':
						case 'parent_url':
						case 'excerpt':
						case 'subtitle':
						case 'content':
						case 'status':
						case 'publishdate':
						case 'datecreated':
							$result[$index][$field] = $value;
							break;

						case 'url':
							$result[$index][$field] = $value;
							$result[$index]['link'] = \dash\url::kingdom(). '/'. $value;
							break;
					}
				}
			}
		}

		return $result;

	}



}
?>