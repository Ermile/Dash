<?php
namespace lib\mvc\models;

trait breadcrumb
{
	/**
	 * create breadcrumb and location of it
	 * @return [type] [description]
	 */
	public function breadcrumb()
	{
		$_addr      = $this->url('breadcrumb');
		$breadcrumb = [];

		foreach ($_addr as $key => $value)
		{
			if($key > 0)
				$breadcrumb[] = strtolower("{$breadcrumb[$key-1]}/$value");
			else
				$breadcrumb[] = strtolower("$value");
		}

		$qry         = $this->sql()->table('posts')->where('post_url', 'IN' , "('".join("' , '", $breadcrumb)."')");
		$qry         = $qry->select();
		$post_titles = $qry->allassoc('post_title');
		$post_urls   = $qry->allassoc('post_url');

		if(count($breadcrumb) != $post_titles)
		{
			$terms_qry   = $this->sql()->table('terms')->where('term_url', 'IN' , "('".join("' , '", $breadcrumb)."')");
			$terms_qry   = $terms_qry->select();
			$term_titles = $terms_qry->allassoc('term_title');
			$term_urls   = $terms_qry->allassoc('term_url');
		}

		$br = [];
		foreach ($breadcrumb as $key => $value)
		{
			$post_key = array_search($value, $post_urls);
			$term_key = array_search($value, $term_urls);
			if($post_key !== false && isset($post_titles[$post_key]))
			{
				$br[] = $post_titles[$post_key];
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
		$qry = $qry->select()->allassoc();
		if(!$qry)
		{
			return $_addr;
		}
		$br = [];
		foreach ($breadcrumb as $key => $value)
		{
			if ($value != $qry[$key]['post_url'])
			{
				$br[] = $_addr[$key];
				array_unshift($qry, '');
			}
			else
			{
				$br[] = $qry[$key]['post_title'];
			}
		}
		return $br;
	}


	/**
	 * get the list of pages
	 * @param  boolean $_select for use in select box
	 * @return [type]           return string or dattable
	 */
	public function sp_books_nav()
	{
		$myUrl         = \lib\router::get_url(-1);
		$result        = ['cats' => null, 'pages' => null];
		$parent_search = null;

		switch (count($myUrl))
		{
			// book/book1
			case 2:
				$myUrl  = $this->url('path');
				$parent_search = 'id';
				break;
			// book/book1/jeld1
			case 3:
				$myUrl  = $this->url('path');
				$parent_search = 'parent';
				break;
			// book/book1/jeld1/page1
			case 4:
				$myUrl = $myUrl[0]. '/'. $myUrl[1]. '/'. $myUrl[2];
				$parent_search = 'parent';
				break;
			// on other conditions return false
			default:
				return false;
		}

		// get id of current page
		$qry = $this->sql()->table('posts')
			->where('post_type', 'book')
			->and('post_url', $myUrl)
			->and('post_status', 'publish')
			->field('id', '#post_parent as parent')
			->select();
		if($qry->num() != 1)
			return;

		$datarow = $qry->assoc();

		// get list of category or jeld
		$qry = $this->sql()->table('posts')
			->where('post_type', 'book')
			->and('post_status', 'publish')
			->and('post_parent', $datarow[$parent_search])
			->field('id', '#post_title as title', '#post_parent as parent', '#post_url as url')
			->select();
		if($qry->num() < 1)
			return;

		$result['cats'] = $qry->allassoc();
		$catsid         = $qry->allassoc('id');
		$catsid         = implode($catsid, ', ');

		// check has page on category or only in
		$qry2 = $this->sql()->table('posts')
			->where('post_type', 'book')
			->and('post_status', 'publish')
			->and('post_parent', 'IN', '('. $catsid. ')')
			->field('id');

		$qry2            = $qry2->select();
		$result['pages'] = $qry2->num();

		return $result;
	}
}
?>
