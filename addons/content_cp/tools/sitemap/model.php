<?php
namespace addons\content_cp\tools\sitemap;
use \lib\utility;

class model extends \mvc\model
{

	public function post_sitemap()
	{
		var_dump(22);
		return 57;
	}


	function generate_sitemap()
	{
		// create sitemap for each language
		$result   = '';

		$site_url = \lib\router::get_storage('url_site');
		$result   .= "<pre>";
		$result   .= $site_url.'<br/>';
		$sitemap  = new \lib\utility\sitemap($site_url , root.'public_html/', 'sitemap' );
		$counter  =
		[
			'pages'       => 0,
			'polls'       => 0,
			'posts'       => 0,
			'helps'       => 0,
			'attachments' => 0,
			'otherTypes'  => 0,
			'terms'       => 0,
			// 'cats'        => 0,
			// 'otherTerms'  => 0,
		];

		// --------------------------------------------- Static pages

		// add list of static pages
		$sitemap->addItem('', '1', 'daily');
		$sitemap->addItem('fa', '1', 'daily');


		$sitemap->addItem('about', '0.6', 'weekly');
		$sitemap->addItem('social-responsibility', '0.6', 'weekly');
		$sitemap->addItem('help', '0.4', 'daily');
		$sitemap->addItem('help/faq', '0.6', 'daily');

		$sitemap->addItem('benefits', '0.6', 'weekly');
		$sitemap->addItem('pricing', '0.6', 'weekly');
		$sitemap->addItem('terms', '0.4', 'weekly');
		$sitemap->addItem('privacy', '0.4', 'weekly');
		$sitemap->addItem('changelog', '0.5', 'daily');
		$sitemap->addItem('contact', '0.6', 'weekly');
		$sitemap->addItem('logo', '0.8', 'monthly');




		// PERSIAN
		// add static pages of persian
		$sitemap->addItem('fa/about', '0.8', 'weekly');
		$sitemap->addItem('fa/social-responsibility', '0.8', 'weekly');
		$sitemap->addItem('fa/help', '0.6', 'daily');
		$sitemap->addItem('fa/help/faq', '0.8', 'daily');

		$sitemap->addItem('fa/benefits', '0.8', 'weekly');
		$sitemap->addItem('fa/pricing', '0.8', 'weekly');
		$sitemap->addItem('fa/terms', '0.6', 'weekly');
		$sitemap->addItem('fa/privacy', '0.6', 'weekly');
		$sitemap->addItem('fa/changelog', '0.7', 'daily');
		$sitemap->addItem('fa/contact', '0.8', 'weekly');
		$sitemap->addItem('fa/logo', '0.8', 'monthly');



		// add posts
		foreach ($this->model()->sitemap('posts', 'post') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.8', 'daily', $row['post_publishdate']);
			$counter['posts'] += 1;
		}

		// // add poll
		// foreach ($this->model()->sitemap('posts', 'poll') as $row)
		// {
		// 	$myUrl = $row['post_url'];
		// 	if($row['post_language'] && $row['post_language'] !== 'en')
		// 	{
		// 		$myUrl = $row['post_language'].'/'. $myUrl;
		// 	}

		// 	if(isset($row['post_privacy']) && $row['post_privacy'] === 'public')
		// 	{
		// 		$sitemap->addItem($myUrl, '0.8', 'daily', $row['post_publishdate']);
		// 		$counter['polls'] += 1;
		// 	}
		// }

		// add pages
		foreach ($this->model()->sitemap('posts', 'page') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.6', 'weekly', $row['post_publishdate']);
			$counter['pages'] += 1;
		}

		// add helps
		foreach ($this->model()->sitemap('posts', 'helps') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.3', 'monthly', $row['post_publishdate']);
			$counter['helps'] += 1;
		}

		// add attachments
		foreach ($this->model()->sitemap('posts', 'attachment') as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.2', 'weekly', $row['post_publishdate']);
			$counter['attachments'] += 1;
		}

		// add other type of post
		foreach ($this->model()->sitemap('posts', false) as $row)
		{
			$myUrl = $row['post_url'];
			if($row['post_language'] && $row['post_language'] !== 'en')
			{
				$myUrl = $row['post_language'].'/'. $myUrl;
			}

			$sitemap->addItem($myUrl, '0.5', 'weekly', $row['post_publishdate']);
			$counter['otherTypes'] += 1;
		}

		// add cats and tags
		foreach ($this->model()->sitemap('terms') as $row)
		{
			$myUrl = $row['term_url'];
			if($row['term_language'])
			{
				$myUrl = $row['term_language'].'/'. $myUrl;
			}


			$sitemap->addItem($myUrl, '0.4', 'weekly', $row['date_modified']);
			$counter['terms'] += 1;
		}

		$sitemap->createSitemapIndex();
		$result .= "</pre>";
		$result .= "<p class='alert alert-success'>". T_('Create sitemap Successfully!')."</p>";

		foreach ($counter as $key => $value)
		{
			$result .= "<br/>";
			$result .= T_($key). " <b>". $value."</b>";
		}

		return $result;
	}



	public function sitemap($_table = 'posts', $_type = null)
	{
		$prefix = substr($_table, 0, -1);
		$status = $_table === 'posts'? 'publish': 'enable';
		$date   = $_table === 'posts'? 'post_publishdate': 'date_modified';
		$lang   = $_table === 'posts'? 'post_language': 'term_language';
		$qry    = $this->sql()->table($_table)->where($prefix.'_status', $status);
		if($_type)
		{
			$qry = $qry->and($prefix.'_type', $_type);
		}
		elseif($_type === false && $_table === 'posts')
		{
			$qry = $qry->and($prefix.'_type', '<>', "'post'");
			// $qry = $qry->and($prefix.'_type', '<>', "'poll'");
			// $qry = $qry->and($prefix.'_type', '<>', "'survey'");
			$qry = $qry->and($prefix.'_type', '<>', "'page'");
			$qry = $qry->and($prefix.'_type', '<>', "'help'");
			$qry = $qry->and($prefix.'_type', '<>', "'attachment'");
		}

		if($_table === 'posts')
		{
			// $qry = $qry->field($prefix.'_url', $date, $lang, 'post_privacy')->order('id','DESC');
			$qry = $qry->field($prefix.'_url', $date, $lang)->order('id','DESC');
		}
		else
		{
			$qry = $qry->field($prefix.'_url', $date, $lang)->order('id','DESC');
		}
// var_dump($qry->selectString());exit();
		return $qry->select()->allassoc();
	}


}
?>