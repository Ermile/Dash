<?php
namespace dash\app;

class template
{
	public static $display_name    = null;
	public static $finded_template = null;
	public static $datarow         = null;
	public static $file_ext        = '.html';
	public static $display_prefix  = true;


	public static function find()
	{
		// if is unload request need less to run template
		if(\dash\request::is_unload())
		{
			return;
		}

		// find support link if exist
		if(self::support_link())
		{
			// redirect
			return true;
		}

		// finded the social short link
		if(self::social_short_link())
		{
			// redirect
			return true;
		}

		$data  = null;
		$slug  = null;
		$type  = null;
		$table = null;

		// load simillary about or about-fa .html
		if(self::fake_static_page())
		{
			self::$finded_template = true;
			return true;
		}

		// this project needless to find database template
		if(!\dash\option::config('cms'))
		{
			return false;
		}

		if($data = self::find_cat())
		{
			// find if 'categroy' is the first of url
			$type  = 'category';
			$table = 'terms';
			if(isset($data['slug']))
			{
				$slug = $data['slug'];
			}
		}
		elseif($data = self::find_tag())
		{
			// find if 'tag' is the first of url
			$type  = 'tag';
			$table = 'terms';
			if(isset($data['slug']))
			{
				$slug = $data['slug'];
			}
		}
		elseif($data = self::find_post())
		{
			// find the post by this url
			$type  = 'post';
			$table = 'posts';
			if(isset($data['slug']))
			{
				$slug = $data['slug'];
			}

			if(isset($data['type']))
			{
				$type = $data['type'];
			}

			$not_allow_type_route = ['help'];

			if(in_array($type, $not_allow_type_route))
			{
				return false;
			}


			if(is_callable(['\\lib\\app\\posts\\template', 'find']))
			{
				\lib\app\posts\template::find($data);
			}

			if(isset($data['meta']['redirect']) && $data['meta']['redirect'])
			{
				\dash\redirect::to($data['meta']['redirect'], true, 302);
			}

		}
		elseif ($data = self::find_term())
		{

			$type  = 'cat';
			$table = 'terms';
			$url   = null;
			$lang  = null;

			if(isset($data['url']))
			{
				$url = $data['url'];
			}

			if(isset($data['type']))
			{
				$type = $data['type'];
			}

			if($type === 'cat')
			{
				$type = 'category';
			}

			if(isset($data['language']))
			{
				$lang = '/'. $data['language'];
			}

			$new_url = \dash\url::base(). $lang. '/'. $type. '/'. $url;

			\dash\redirect::to($new_url);
			return;

		}
		elseif(self::find_404())
		{
			// show customize 404 page
			return true;
		}

		if($type && $slug && $table)
		{
			self::set_display_name($data, $type, $slug, $table);
			return true;
		}
		else
		{
			return false;
		}

	}


	public static function set_display_name($data, $type, $slug, $table)
	{
		$finded_template = false;
		$contentAddr = \dash\engine\content::get_addr();

		// elseif template type with specefic slug exist show it
		if( is_file($contentAddr. 'template/'.$type.'-'. $slug. self::$file_ext) )
		{
			self::$display_name	= $type.'-'.$slug. self::$file_ext;
		}
		// elseif template type with name of table exist in module folder then show it
		elseif( is_file($contentAddr. $type.'/'.$table.self::$file_ext) )
		{
			self::$display_name   = $type.'/'.$table.self::$file_ext;
			self::$display_prefix = null;
		}
		// elseif template type with name of table exist show it
		elseif( is_file($contentAddr. 'template/'.$type.'-'.$table.self::$file_ext) )
		{
			self::$display_name	= $type.'-'.$table.self::$file_ext;
		}
		// elseif template type exist show it like posts or terms
		elseif( is_file($contentAddr. 'template/'.$type.self::$file_ext) )
		{
			self::$display_name	= $type.self::$file_ext;
		}
		// elseif template cat exist show it
		// elseif( is_file($contentAddr. 'template/'.$post_cat.self::$file_ext) )
		// {
		// 	self::$display_name	= $post_cat.self::$file_ext;
		// }

		// elseif template type exist show it
		elseif( is_file($contentAddr. 'template/'.$table.self::$file_ext) )
		{
			self::$display_name	= $myurl['table'].self::$file_ext;
		}
		// elseif default template exist show it else use homepage!
		elseif( is_file($contentAddr. 'template/default'. self::$file_ext) )
		{
			self::$display_name	= 'default'. self::$file_ext;
		}
		// below line is depreacted and remove on next release!
		elseif( is_file($contentAddr. 'template/dafault'. self::$file_ext) )
		{
			self::$display_name	= 'dafault'. self::$file_ext;
		}
		// if find template for this url
		// then if template for current lang is exist, set it
		if(self::$display_name)
		{
			self::checkLangTemplate();

			$finded_template = true;
		}

		if(isset($data['meta']) && is_string($data['meta']) && substr($data['meta'], 0,1) === '{')
		{
			$data['meta'] = json_decode($data['meta'], true);
		}

		if($finded_template)
		{
			self::$datarow = $data;
			self::$finded_template = $finded_template;
		}
	}


	public static function support_link()
	{
		$mymodule = \dash\url::module();
		if(substr($mymodule, 0, 1) === '!')
		{
			$supportCode = substr($mymodule, 1);
			// create url of support
			$support_link = \dash\url::kingdom(). '/support/ticket/show?id='.$supportCode;
			// redirect to new address
			\dash\redirect::to($support_link);
			return true;
		}

		return false;
	}


	public static function social_short_link()
	{
		// save name of current module as name of social
		$mymodule    = \dash\url::module();
		$social_name = $mymodule;

		if(\dash\option::social('status'))
		{
			// declare list of shortkey for socials
			$social_list =
			[
				'@'        => 'twitter',
				'~'        => 'github',
				'+'        => 'googleplus',
				'f'        => 'facebook',
				'fb'       => 'facebook',
				'in'       => 'linkedin',
				'tg'       => 'telegram',
			];

			// if name of current module is exist then save complete name of it
			if(isset($social_list[$mymodule]))
			{
				$social_name = $social_list[$mymodule];
			}
		}

		// declare address of social networks
		$social_list =
		[
			'twitter'    => 'https://twitter.com/',
			'github'     => 'https://github.com/',
			'googleplus' => 'https://plus.google.com/',
			'facebook'   => 'https://www.facebook.com/',
			'linkedin'   => 'https://linkedin.com/in/',
			'telegram'   => 'http://telegram.me/',
			'aparat'     => 'http://www.aparat.com/',
		];

		// if social name exist in social adresses then redirect to it
		if(
			isset($social_list[$social_name]) &&
			\dash\option::social($social_name, 'name') &&
			is_string(\dash\option::social($social_name, 'name'))
		  )
		{
			// create url of social network
			$social_url = $social_list[$social_name] . \dash\option::social($social_name, 'name');
			// redirect to new address
			\dash\redirect::to($social_url);
			return true;
		}

		return false;
	}


	public static function fake_static_page()
	{
		$mymodule    = \dash\url::module();
		$contentAddr = \dash\engine\content::get_addr();

		// if user entered url contain one of our site language
		$current_path = \dash\url::dir();
		if(is_array($current_path))
		{
			$current_path = implode('/', $current_path);
		}

		// if custom template exist show this template
		if( is_file($contentAddr. 'template/static_'. $current_path. self::$file_ext) )
		{
			self::$display_name = 'static_'. $current_path. self::$file_ext;

		}
		elseif( is_file($contentAddr. 'template/static/'. $current_path. self::$file_ext) )
		{
			self::$display_name = 'static\\'. $current_path. self::$file_ext;

		}
		else
		{
			// create special url for handle special type of syntax
			// for example see below example
			// ermile.com/legal			 	-> content/template/legal/home.html
			// ermile.com/legal/privacy		-> content/template/legal/privacy.html
			$my_special_url = substr($current_path, strlen($mymodule)+1);
			if(!$my_special_url)
			{
				$my_special_url = 'home';
			}
			$my_special_url = $mymodule. '/'. $my_special_url;
			if(is_file($contentAddr. 'template/static/'. $my_special_url. self::$file_ext))
			{
				self::$display_name = 'static\\'. $my_special_url. self::$file_ext;

			}
		}

		if(self::$display_name)
		{
			self::checkLangTemplate();

			return true;
		}

		return false;

	}



	private static function checkLangTemplate()
	{
		if(self::$display_prefix === true)
		{
			self::$display_name = \dash\engine\content::get(). '\template\\'. self::$display_name;
		}
		else if(self::$display_prefix === null)
		{
			self::$display_name = \dash\engine\content::get(). '\\'. self::$display_name;
		}

		$current_lang = \dash\language::current('name');

		$current_lang_template = substr(self::$display_name, 0, -(strlen(self::$file_ext)));
		$current_lang_template .= '-'.$current_lang . self::$file_ext;

		$current_lang_template = str_replace("\\", DIRECTORY_SEPARATOR, $current_lang_template);
		$current_lang_template = str_replace("/", DIRECTORY_SEPARATOR, $current_lang_template);

		if(is_file(root.$current_lang_template))
		{
			self::$display_name	= $current_lang_template;
		}
	}


	private static function get_my_url()
	{
		$myUrl = \dash\url::directory();
		$myUrl = \dash\url::urlfilterer($myUrl);
		return $myUrl;
	}


	public static function find_cat()
	{
		$myUrl = self::get_my_url();

		if(substr($myUrl, 0, 9) === 'category/')
		{
			$cat_data = \dash\db\terms::get(['url' => substr($myUrl, 9), 'type' => 'cat', 'language' => \dash\language::current(), 'limit' => 1]);
			if($cat_data)
			{
				return $cat_data;
			}
		}
		return false;
	}


	public static function find_tag()
	{
		$myUrl = self::get_my_url();
		if(substr($myUrl, 0, 4) === 'tag/')
		{
			$cat_data = \dash\db\terms::get(['url' => substr($myUrl, 4), 'type' => 'tag', 'language' => \dash\language::current(), 'limit' => 1]);
			if($cat_data)
			{
				return $cat_data;
			}
		}
		return false;
	}


	public static function find_term()
	{
		$myUrl = self::get_my_url();

		if(self::ignore_url($myUrl))
		{
			return false;
		}

		$term_data = \dash\db\terms::get(['url' => $myUrl, 'type' => 'cat', 'language' => \dash\language::current(), 'limit' => 1]);

		if($term_data)
		{
			return $term_data;
		}

		$term_data = \dash\db\terms::get(['url' => $myUrl, 'type' => 'tag', 'language' => \dash\language::current(), 'limit' => 1]);

		if($term_data)
		{
			return $term_data;
		}
		return false;
	}


	public static function find_post()
	{
		if(defined('db_name') && !empty(db_name))
		{
			$post_detail = \dash\app\posts::find_post();
			if($post_detail)
			{
				return $post_detail;
			}
		}
		return false;
	}


	public static function find_404()
	{
		if( is_file(root.'content/template/404.html') )
		{
			// header("HTTP/1.1 404 NOT FOUND");
			self::$display_name	= 'content\\template\\404.html';
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	* some url is ignored
	*/
	public static function ignore_url($_url)
	{
		if(substr($_url, 0, 7) === 'static/')
		{
			return true;
		}

		if(substr($_url, 0, 6) === 'files/')
		{
			return true;
		}

		return false;
	}

}
?>