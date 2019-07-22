<?php
namespace dash\engine;

class twigAddons
{

	public static function init($_twig)
	{
		$filters   = [];
		$filters[] = self::filter_fcache();
		$filters[] = self::filter_jdate();
		$filters[] = self::filter_dt();
		$filters[] = self::filter_readableSize();
		$filters[] = self::filter_fitNumber();
		$filters[] = self::filter_exist();
		$filters[] = self::filter_decode();
		$filters[] = self::filter_coding();
		$filters[] = self::filter_filemtime();
		$filters[] = self::filter_unset_type();
		$filters[] = self::filter_var_dump();

		foreach ($filters as $key => $value)
		{
			$_twig->addFilter($value);
		}


		$functions   = [];
		$functions[] = self::function_breadcrumb();
		$functions[] = self::function_langList();
		$functions[] = self::function_posts();
		$functions[] = self::function_tags();
		$functions[] = self::function_category();
		$functions[] = self::function_comments();
		$functions[] = self::function_attachment();
		$functions[] = self::function_post_search();
		$functions[] = self::function_perm();
		$functions[] = self::function_perm_su();

		foreach ($functions as $key => $value)
		{
			$_twig->addFunction($value);
		}
	}


	private static function filter_var_dump()
	{
		return new \Twig_SimpleFilter('dump', 'var_dump');
	}


	private static function filter_unset_type()
	{
		return new \Twig_SimpleFilter('unset_type', function ($array= null)
		{
			unset($array['attr']['type']);
			return $array;
		});
	}


	private static function filter_fcache()
	{
		return new \Twig_SimpleFilter('fcache', function ($string)
		{
			if(file_exists($string))
			{
				return $string.'?'.filemtime($string);
			}
			elseif(file_exists(root . 'public_html/'.$string))
			{
				return $string.'?'.filemtime(root . 'public_html/'.$string);
			}
		});
	}


	private static function filter_jdate()
	{
		return new \Twig_SimpleFilter('jdate', function ($_string, $_format ="Y/m/d", $_convert = true)
		{
			return \dash\utility\jdate::date($_format, $_string, $_convert);
		});
	}




	/**
	 * twig custom filter for convert datetime to best type of showing on each language
	 * dt means datetime
	 */
	private static function filter_dt()
	{
		return new \Twig_SimpleFilter('dt', function ($_string, $_format = null, $_type = null, $_calendar = null)
		{
			if(!$_string)
			{
				return null;
			}

			return \dash\datetime::fit($_string, $_format, $_type, $_calendar);

		});
	}




	/**
	 * twig custom filter for convert date to jalai with custom format like php date func format
	 */
	private static function filter_readableSize()
	{
		return new \Twig_SimpleFilter('readableSize', function ($_string, $_type = 'file', $_emptyTxt = null)
		{
			return \dash\utility\upload::readableSize($_string, $_type, $_emptyTxt);
		});
	}


	/**
	 * twig custom filter for convert date to jalai with custom format like php date func format
	 */
	private static function filter_fitNumber()
	{
		return new \Twig_SimpleFilter('fitNumber', function ($_number, $_autoFormat = true)
		{
			return \dash\utility\human::fitNumber($_number, $_autoFormat);
		});
	}


	/**
	 * [filter_exist description]
	 * @return [type] [description]
	 */
	private static function filter_exist()
	{
		return new \Twig_SimpleFilter('exist', function ($_file, $_alternative = null)
		{
			$result = \dash\file::alternative($_file, $_alternative);
			return $result;
		});
	}


	/**
	 * [filter_decode description]
	 * @return [type] [description]
	 */
	private static function filter_decode()
	{
		return new \Twig_SimpleFilter('decode', function ($_array, $_key = null)
		{
			$result = json_decode($_array, true);
			if(is_array($result) && isset($result[$_key]))
			{
				$result = $result[$_key];
			}
			else
			{
				$result = $_array;
			}

			return $result;
		});
	}


	/**
	 * twig custom filter for dump with php
	 */
	private static function function_dump()
	{
		return new \Twig_SimpleFunction('dump', function()
		{

		});
	}



	/**
	 * [function_language description]
	 * @return [type] [description]
	 */
	private static function function_langList()
	{
		return new \Twig_SimpleFunction('langList', function()
		{
			return \dash\language::langList(...func_get_args());
		});
	}


	/**
	 * [function_breadcrumb description]
	 * @return [type] [description]
	 */
	private static function function_breadcrumb()
	{
		return new \Twig_SimpleFunction('breadcrumb', function ($_path = null, $_direct = null, $_homepage = true, $_hideLast = null)
		{
			// if user dont pass a path give it from controller
			if(!$_path)
			{
				$myurl =  []; //\dash\utility\breadcrumb::get();
				$_path = \dash\url::dir();
			}
			$direct = null;
			if($_direct === true)
			{
				$direct = "data-direct";
			}

			$currentUrl = null;
			$result     = '';
			if($_homepage || count($myurl))
			{
				$baseURL    = \dash\url::base();
				if(\dash\url::content() === null)
				{
					$result = '<a href="'. $baseURL. '" tabindex="-1" '. $direct.'><span class="fa fa-home"></span> '.T_('Homepage').'</a>';
				}
				else
				{
					$myContent     = \dash\url::content();
					$myContentName = $myContent;
					// if contetent name is exist use it as alternative
					if(\dash\data::get('breadcrumb', $myContent))
					{
						$myContentName = \dash\data::get('breadcrumb', $myContent);
					}
					elseif($myContent === 'cp')
					{
						$myContentName = 'Control Panel';
					}
					$result = '<a href="'. $baseURL. '" tabindex="-1" '. $direct.'><span class="fa fa-home"></span> '.T_('Home').'</a>';
					$result .= '<a href="'. $baseURL.'/'. $myContent. '" tabindex="-1" '. $direct.'>'.T_($myContentName).'</a>';
				}

			}

			foreach ($myurl as $key => $part)
			{
				$currentUrl  .= $_path[$key].'/';
				$baseURLFull = \dash\url::here();
				$anchorUrl   = trim($baseURLFull.'/'.$currentUrl, '/');
				$location    = $part;
				// set title of each locations
				if(\dash\data::get('breadcrumb', $location))
				{
					$location = \dash\data::get('breadcrumb', $location);
				}

				// if trans of exact text is exist use it
				if($location != T_($location))
				{
					$location = T_($location);
				}
				// else change all chars to lower and check to find trans, if exist use it
				elseif($location != T_(mb_strtolower($location)))
				{
					$location = T_(mb_strtolower($location));
					$location = ucwords($location);
				}
				// else change it to good text
				else
				{
					$location    = str_replace('-', ' ', $location);
					$location    = ucwords($location);
					$location    = str_replace('And', 'and', $location);
					$location    = T_($location);
				}

				if(end($myurl) === $part)
				{
					if($_hideLast)
					{
						// do nothing
					}
					else
					{
						$result .= "<a>$location</a>";
					}
				}
				else
				{
					$result .= "<a href='$anchorUrl' tabindex='-1'>". $location. "</a>";
				}
			}

			echo $result;
		});
	}


	/**
	 * [function_posts description]
	 * @return [type] [description]
	 */
	private static function function_posts()
	{
		return new \Twig_SimpleFunction('posts', function()
		{
			$posts  = \dash\app\posts::get_post_list(...func_get_args());
			$html   = array_column(func_get_args(), 'html');
			$desc   = array_column(func_get_args(), 'desc');
			if($html && count($html) === 1)
			{
				$html = $html[0];
			}

			if($desc && count($desc) === 1)
			{
				$desc = $desc[0];
			}

			if($html)
			{
				$counter = 0;
				$result  = '';
				$content = '';
				foreach ($posts as $item)
				{
					$result .= "\n    ";
					$result .= "<article>";

					if($desc == 'all' || (is_numeric($desc) && $desc > $counter))
					{
						$result .= "<a href='/".$item['url']."'>".$item['title']."</a>";
						if(isset($item['content']))
						{
							$content = \dash\utility\excerpt::get($item['content']);
							if($content)
							{
								$result .= '<p>'. $content .'</p>';
							}
						}
					}
					else
					{
						$result .= "<a href='/".$item['url']."'>".$item['title']."</a>";
					}
					$result .= "</article>";
					// increase counter
					$counter++;
				}

				echo $result;
			}
			else
			{
				return $posts;
			}

		});
	}


	/**
	 * [function_posts description]
	 * @return [type] [description]
	 */
	private static function function_tags()
	{
		return new \Twig_SimpleFunction('tags', function()
		{
			$tags      = [];
			$args      = func_get_args();
			$container = '';
			if(isset($args[0]))
			{
				$args = $args[0];
			}

			// get post id
			if(!isset($args['post_id']))
			{
				if(\dash\data::datarow_id())
				{
					$args['post_id'] = \dash\data::datarow_id();
				}
			}

			// get related
			$related = 'posts';
			if(isset($args['related']) && is_string($args['related']) && $args['related'])
			{
				$related = $args['related'];
			}

			$type = 'tag';
			if(isset($args['type']) && is_string($args['type']) && $args['type'])
			{
				$type = $args['type'];
			}

			// get tags
			if(isset($args['post_id']))
			{
				$cache_key = 'post_tag_'. $args['post_id'];
				if(\dash\temp::get($cache_key))
				{
					$tags = \dash\temp::get($cache_key);

				}
				else
				{
					$tags = \dash\app\posts::get_category_tag($args['post_id'], $type, $related);
					\dash\temp::set($cache_key, $tags);
				}
			}

			if(isset($args['title']) && $args['title'])
			{
				if(is_array($tags))
				{
					$tags = array_column($tags, 'title');
				}
			}

			if(isset($args['container']) && $args['container'])
			{
				$container = $args['container'];
			}


			if(isset($args['format']) && $args['format'])
			{
				$outputFormat = $args['format'];
				switch ($outputFormat)
				{
					case 'array':
						return $tags;
						break;

					case 'json':
						if(is_array($tags))
						{
							$tags = json_encode($tags, JSON_UNESCAPED_UNICODE);
						}
						break;

					case 'csv':
						if(is_array($tags))
						{
							$tags = implode(',', $tags);
						}
						break;

					case 'html':
					case 'html2':
						$html = '';
						if(is_array($tags) && $tags)
						{

							if($container)
							{
								$html = "<div class='$container'>";
							}

							foreach ($tags as $key => $value)
							{
								$baset_url = \dash\url::base();

								if($value['language'] !== \dash\language::primary())
								{
									$baset_url .= '/'. $value['language'];
								}

								if(array_key_exists('url', $value) && array_key_exists('slug', $value) && isset($value['title']))
								{
									if($outputFormat === 'html2')
									{
										$html .= "<span title='$value[slug]'>$value[title]</span>";
									}
									else
									{
										$html .= "<a href='$baset_url/tag/$value[url]'>$value[title]</a>";
									}
								}
							}
							// close container
							if($container)
							{
								$html .= '</div>';
							}
						}
						elseif(is_string($tags))
						{
							$html = $tags;
						}
						echo $html;
						// return and dont continue
						return;
						break;

					default:
						break;
				}

			}

			if($tags)
			{
				return $tags;
			}
		});
	}


	/**
	 * [function_posts description]
	 * @return [type] [description]
	 */
	private static function function_category()
	{
		return new \Twig_SimpleFunction('category', function()
		{
			$category = [];
			$args     = func_get_args();
			$class    = '';
			$type     = 'cat';

			if(isset($args[0]))
			{
				$args = $args[0];
			}

			if(isset($args['type']))
			{
				$type = $args['type'];
			}

			// get post id
			if(!isset($args['post_id']))
			{
				if(\dash\data::datarow_id())
				{
					$args['post_id'] = \dash\data::datarow_id();
				}
			}
			// get category
			if(isset($args['post_id']))
			{
				$category = \dash\app\posts::get_category_tag($args['post_id'], $type);
			}

			if(isset($args['id']) && $args['id'] && is_array($category))
			{
				$category = array_column($category, 'term_id');
			}

			if(isset($args['class']) && $args['class'])
			{
				$class = $args['class'];
			}

			if(isset($args['format']) && $args['format'])
			{
				$outputFormat = $args['format'];

				switch ($outputFormat)
				{
					case 'json':
						if(is_array($category))
						{
							$category = json_encode($category, JSON_UNESCAPED_UNICODE);
						}
						break;

					case 'csv':
						if(is_array($category))
						{
							$category = implode(',', $category);
						}
						break;

					case 'html':
						$html      = '';
						$baset_url = \dash\url::base();
						foreach ($category as $key => $value)
						{
							if(array_key_exists('url', $value) && isset($value['title']))
							{
								if($class)
								{
									$html .= "<a href='$baset_url/category/$value[url]' class='$class'>$value[title]</a>";
								}
								else
								{
									$html .= "<a href='$baset_url/category/$value[url]'>$value[title]</a>";
								}
							}
						}
						echo $html;
						// return and dont continue
						return;
						break;

					default:
						break;
				}
			}

			return $category;
		});
	}

	/**
	 * [function_posts description]
	 * @return [type] [description]
	 */
	private static function function_comments()
	{
		return new \Twig_SimpleFunction('comments', function()
		{
			$comments = [];
			$args = func_get_args();
			if(isset($args[0]))
			{
				$args = $args[0];
			}

			// get post id
			if(!isset($args['post_id']))
			{
				if(\dash\data::datarow_id())
				{
					$args['post_id'] = \dash\data::datarow_id();
				}
			}
			// count of show comments
			$limit = 6;
			if(isset($args['limit']))
			{
				$limit = $args['limit'];
			}

			// get comments
			if(isset($args['post_id']))
			{
				$post_id = \dash\coding::decode($args['post_id']);
				if($post_id)
				{
					$comments = \dash\db\comments::get_comment($post_id, $limit, \dash\user::id());
				}
			}
			return $comments;
		});
	}


	/**
	 * [function_posts description]
	 * @return [type] [description]
	 */
	private static function function_post_search()
	{
		return new \Twig_SimpleFunction('post_search', function()
		{
			$post_search = [];
			$args = func_get_args();
			if(isset($args[0]))
			{
				$args = $args[0];
			}
			$post_search = \dash\db\posts::search(null, $args);
			return $post_search;

		});
	}



	/**
	 * [twig coding decode|encode]
	 * @return [type] [description]
	 */
	private static function filter_coding()
	{
		return new \Twig_SimpleFilter('coding', function ($_url, $_type = 'decode', $_alphabet = null)
		{
			$result = null;
			if($_type === 'decode')
			{
				$result = \dash\coding::decode($_url, $_alphabet);
			}
			elseif($_type === 'encode')
			{
				$result = \dash\coding::encode($_url, $_alphabet);
			}
			return $result;
		});
	}


	private static function filter_filemtime()
	{
		return new \Twig_SimpleFilter('filemtime', function ($_url, $_withReturn = null)
		{
			$result       = '';
			$lastTime     = null;
			$complete_url = root.'public_html/';
			if($_withReturn === 'siftal')
			{
				$complete_url .= 'static/siftal';
			}
			else if($_withReturn)
			{
				$complete_url .= 'static';
			}
			$complete_url .= $_url;
			if($_url && \dash\file::exists($complete_url))
			{
				$lastTime = filemtime($complete_url);
			}

			if($_withReturn)
			{
				$result = $_url;
				if($lastTime)
				{
					$result .= '?'. $lastTime;
				}
			}

			return $result;
		});
	}


	/**
	 * return tha attachment record of post
	 *
	 * @return     \     ( description_of_the_return_value )
	 */
	private static function function_attachment()
	{
		return new \Twig_SimpleFunction('attachment', function()
		{
			$attachment = [];
			$args       = func_get_args();

			if(isset($args[0]))
			{
				$args = $args[0];
			}

			$get_url = false;
			if(isset($args['url']) && $args['url'] === true)
			{
				$get_url = true;
			}

			if(isset($args['id']))
			{
				$attachment = \dash\db\posts::get_one($args['id']);
				if(isset($attachment['post_type']) && $attachment['post_type'] != 'attachment')
				{
					return [];
				}

				if(is_array($attachment))
				{
					$tmp_attachment = [];
					foreach ($attachment as $key => $value)
					{
						$tmp_attachment[str_replace('post_', '', $key)] = $value;
					}
					$attachment = $tmp_attachment;
				}
			}
			if($get_url)
			{
				if(isset($attachment['meta']['url']))
				{
					return  \dash\url::site().'/'. $attachment['meta']['url'];
				}
			}
			return $attachment;
		});
	}


	/**
	 * return tha attachment record of post
	 *
	 * @return     \     ( description_of_the_return_value )
	 */
	private static function function_perm()
	{
		return new \Twig_SimpleFunction('perm', function()
		{
			$caller  = null;
			$user_id = null;
			$args    = func_get_args();

			if(isset($args[0]))
			{
				$caller = $args[0];
			}

			if(isset($args[1]))
			{
				$user_id = $args[1];
			}

			return \dash\permission::check($caller, $user_id);
		});
	}


	/**
	 * return tha attachment record of post
	 *
	 * @return     \     ( description_of_the_return_value )
	 */
	private static function function_perm_su()
	{
		return new \Twig_SimpleFunction('perm_su', function()
		{
			return \dash\permission::supervisor();
		});
	}
}
?>