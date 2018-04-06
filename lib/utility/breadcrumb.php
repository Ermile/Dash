<?php
namespace dash\model;

class breadcrumb
{
	/**
	 * create breadcrumb and location of it
	 * @return [type] [description]
	 */
	public static function get()
	{

		$_addr      = \dash\url::dir();
		$breadcrumb = [];

		foreach ($_addr as $key => $value)
		{
			if($key > 0)
			{
				$breadcrumb[] = strtolower("{$breadcrumb[$key-1]}/$value");
			}
			else
			{
				$breadcrumb[] = strtolower("$value");
			}
		}

		// $qry = \dash\db\posts::get(['url', ["IN",  "('".join("' , '", $breadcrumb)."')"]]);
		$titles    = [];
		$post_urls = [];

		// if(is_array($qry))
		// {
		// 	$titles    = array_column($qry, 'title');
		// 	$post_urls = array_column($qry, 'url');
		// }


		if(count($breadcrumb) != $titles)
		{
			// $terms_qry = \dash\db\terms::get(['url', ["IN", "('".join("' , '", $breadcrumb)."')"]]);
			$term_titles = [];
			$term_urls   = [];
			// if(is_array($terms_qry))
			// {
			// 	$term_titles = array_column($terms_qry, 'title');
			// 	$term_urls   = array_column($terms_qry, 'url');
			// }
		}

		$br = [];
		foreach ($breadcrumb as $key => $value)
		{
			$post_key = array_search($value, $post_urls);
			$term_key = array_search($value, $term_urls);
			if($post_key !== false && isset($titles[$post_key]))
			{
				$br[] = $titles[$post_key];
			}
			elseif($term_key !== false && isset($term_titles[$term_key]))
			{
				$br[] = $term_titles[$term_key];
			}
			else
			{
				$br[] = $_addr[$key];
			}
		}
		return $br;

		// $qry = $qry->select()->allassoc();
		// if(!$qry)
		// {
		// 	return $_addr;
		// }
		// $br = [];
		// foreach ($breadcrumb as $key => $value)
		// {
		// 	if ($value != $qry[$key]['url'])
		// 	{
		// 		$br[] = $_addr[$key];
		// 		array_unshift($qry, '');
		// 	}
		// 	else
		// 	{
		// 		$br[] = $qry[$key]['title'];
		// 	}
		// }
		// return $br;
	}

}
?>
