<?php
namespace lib\mvc\models;

trait posts
{
	/**
	 * this function return the number of post needed with special condition
	 * @param  [type] $args argumats can pass from twig
	 * @return [type]       the array contain list of posts
	 */
	public function posts(...$args)
	{
		$qry = $this->sql()->tablePosts();

		// check passed value for exist and use it ----------------------------- number of post and offset
		// if pass number of records needed in first param
		if(isset($args[0]) && is_numeric($args[0]))
		{
			// if pass offset as 2nd param
			if(isset($args[1]) && is_numeric($args[1]))
				$qry = $qry->limit($args[1], $args[0]);
			// else if only pass record needed without offset
			else
				$qry = $qry->limit($args[0]);
		}
		// if dont pass through function use default value
		else
			$qry = $qry->limit(10);

		// check passed value for exist and use it ----------------------------- posttype
		$post_type = array_column($args, 'type');
		if( $post_type && count($post_type) === 1)
		{
			$post_type = $post_type[0];
			// do nothing
		}
		// if dont pass through function use default value
		else
		{
			$post_type = 'post';
		}
		$qry = $qry->andPost_type($post_type);


		// check passed value for exist and use it ----------------------------- Language
		$post_language = array_column($args, 'language');
		if( $post_language && count($post_language) === 1)
		{
			$qry = $qry->and('post_language', $post_language[0]);
		}
		// if dont pass through function use default value
		else
		{
			$qry = $qry->groupOpen('g_language');
			$qry = $qry->and('post_language', \lib\define::get_language());
			$qry = $qry->or('post_language', 'IS', 'NULL');
			$qry = $qry->groupClose('g_language');
		}


		// check passed value for exist and use it ----------------------------- orderby
		$post_orderby = array_column($args, 'orderby');
		if( $post_orderby && count($post_orderby) === 1)
			$post_orderby = "order".ucfirst($post_orderby[0]);
		// if dont pass through function use default value
		else
			$post_orderby = "orderId";


		// check passed value for exist and use it ----------------------------- order
		$post_order = array_column($args, 'order');
		if( $post_order && count($post_order) === 1)
		{
			$post_order = $post_order[0];
			if(!is_array($post_order))
				$qry = $qry->$post_orderby($post_order);
		}
		// if dont pass through function use default value
		else
			$qry = $qry->$post_orderby('desc');


		// check passed value for exist and use it ----------------------------- status
		$post_status = array_column($args, 'status');
		if( $post_status && count($post_status) === 1)
		{
			$post_status = $post_status[0];
			// if pass in array splite it and create specefic query
			if(is_array($post_status))
			{
				foreach ($post_status as $value)
				{
					if ($value === reset($post_status))
					{
						$qry = $qry->groupOpen('g_status');
						$qry = $qry->andPost_status($value);
					}
					else
						$qry = $qry->orPost_status($value);
				}
				$qry = $qry->groupClose('g_status');
			}
			// if not array use the passed value
			else
				$qry = $qry->andPost_status($post_status);
		}
		// if dont pass through function use default value
		else
			$qry = $qry->andPost_status('publish');





		// check passed value for exist and use it ----------------------------- category
		$post_cat = array_column($args, 'cat');
		// INNER JOIN termusages ON posts.id = termusages.object_id
		// INNER JOIN terms ON termusages.term_id = terms.id
		// WHERE
		// termusages.termusage_type = 'posts'
		if( $post_cat && count($post_cat) === 1)
		{
			// $qry = $qry->query("SELECT `posts`.* FROM `posts` ");
			$post_cat = $post_cat[0];

			$qry->joinTermusages()->on('termusage_id', '#posts.id')->and('termusage_foreign', '#"posts"')->field(false);
			// $qry->joinTerms()->whereId('#termusages.term_id')->andTerm_slug('#"statements"');

			// $obj = $qry->joinTerms();
			// $obj->whereId('#termusages.term_id')->andTerm_slug('#"statements"');


			$obj = $qry->joinTerms()->on('id', '#termusages.term_id')->field(false)->groupby('#posts.id');
			// $obj->whereTerm_slug('#"statements"');

			// if pass in array splite it and create specefic query
			if(is_array($post_cat))
			{
				foreach ($post_cat as $value)
				{
					$opr = '=';
					if(substr($value, 0, 1) === '-')
					{
						$opr = '<>';
						$value = substr($value, 1);
					}

					if ($value === reset($post_cat))
					{
						// $qry = $qry->groupOpen('g_cat');
						$obj->andTerm_slug($opr, "$value");
					}
					else
					{
						$obj->orTerm_slug($opr, "$value");
					}
				}
				// $qry = $qry->groupClose('g_cat');
			}
			// if not array use the passed value
			else
			{
				$opr = '=';
				if(substr($post_cat, 0, 1) === '-')
				{
					$opr = '<>';
					$post_cat = substr($post_cat, 1);
				}
				$obj->andTerm_slug($opr, "$post_cat");
			}

			// $qry->joinUsers()->whereId('#kids.user_id')
			// 						->fieldUser_firstname("firstname")
		}

		// var_dump($qry->selectString());
		$qry = $qry->select('DISTINCT');
		// echo $qry->string();
		$result = $qry->allassoc();

		// decode and change name of all record to better name
		foreach ($result as $id => $row)
		{
			foreach ($row as $key => $value)
			{
				$pos = strpos($key,'post_');
				if ($pos !== false)
				{
					$fieldName = substr($key, 5);
					if($fieldName === 'content')
					{
						$value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, "UTF-8");
					}
					$result[$id][$fieldName] = $value;
					unset($result[$id][$key]);
				}
			}
		}

		return $result;
	}
}
?>
