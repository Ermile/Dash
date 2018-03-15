<?php
namespace lib\model;

trait breadcrumb
{
	/**
	 * create breadcrumb and location of it
	 * @return [type] [description]
	 */
	public function breadcrumb()
	{

		$_addr      = \lib\url::dir();
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

		// $qry = \lib\db\posts::get(['url', ["IN",  "('".join("' , '", $breadcrumb)."')"]]);
		$titles    = [];
		$post_urls = [];

		// if(is_array($qry))
		// {
		// 	$titles    = array_column($qry, 'title');
		// 	$post_urls = array_column($qry, 'url');
		// }


		if(count($breadcrumb) != $titles)
		{
			// $terms_qry = \lib\db\terms::get(['url', ["IN", "('".join("' , '", $breadcrumb)."')"]]);
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


	/**
	 * get the list of pages
	 * @param  boolean $_select for use in select box
	 * @return [type]           return string or dattable
	 */
	public function sp_books_nav()
	{
		// $myUrl         = \lib\url::dir();
		// $result        = ['cats' => null, 'pages' => null];
		// $parent_search = null;

		// switch (count($myUrl))
		// {
		// 	// book/book1
		// 	case 2:
		// 		$myUrl  = $this->url('path');
		// 		$parent_search = 'id';
		// 		break;
		// 	// book/book1/jeld1
		// 	case 3:
		// 		$myUrl  = $this->url('path');
		// 		$parent_search = 'parent';
		// 		break;
		// 	// book/book1/jeld1/page1
		// 	case 4:
		// 		$myUrl = $myUrl[0]. '/'. $myUrl[1]. '/'. $myUrl[2];
		// 		$parent_search = 'parent';
		// 		break;
		// 	// on other conditions return false
		// 	default:
		// 		return false;
		// }

		// // get id of current page
		// $qry = $this->sql()->table('posts')
		// 	->where('type', 'book')
		// 	->and('url', $myUrl)
		// 	->and('status', 'publish')
		// 	->field('id', '#parent as parent')
		// 	->select();
		// if($qry->num() != 1)
		// 	return;

		// $datarow = $qry->assoc();

		// // get list of category or jeld
		// $qry = $this->sql()->table('posts')
		// 	->where('type', 'book')
		// 	->and('status', 'publish')
		// 	->and('parent', $datarow[$parent_search])
		// 	->field('id', '#title as title', '#parent as parent', '#post_url as url')
		// 	->select();
		// if($qry->num() < 1)
		// 	return;

		// $result['cats'] = $qry->allassoc();
		// $catsid         = $qry->allassoc('id');
		// $catsid         = implode($catsid, ', ');

		// // check has page on category or only in
		// $qry2 = $this->sql()->table('posts')
		// 	->where('type', 'book')
		// 	->and('status', 'publish')
		// 	->and('parent', 'IN', '('. $catsid. ')')
		// 	->field('id');

		// $qry2            = $qry2->select();
		// $result['pages'] = $qry2->num();

		// return $result;
	}
}
?>
