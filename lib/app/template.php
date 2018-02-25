<?php
namespace lib\app;

class template
{
	public static $module           = null;
	public static $display_name     = null;
	public static $route_check_true = null;
	public static $datarow          = null;
	public static $file_ext         = '.html';
	public static $display_prefix   = 'content\template\\';


	public static function find()
	{
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
			self::$route_check_true = true;
			return true;
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
		}
		elseif ($data = self::find_term())
		{

			$type  = 'cat';
			$table = 'terms';
			$url   = null;

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

			$new_url = \lib\url::base(). '/'. $type. '/'. $url;

			(new \lib\redirector($new_url))->redirect();
			return;

		}
		elseif(self::find_404())
		{
			// no way to load page
			return false;
			// :(
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

		$route_check_true = false;

		// elseif template type with specefic slug exist show it
		if( is_file(root.'content/template/'.$type.'-'. $slug. self::$file_ext) )
		{
			self::$display_name	= $type.'-'.$slug. self::$file_ext;
		}
		// elseif template type with name of table exist in module folder then show it
		elseif( is_file(root.'content/'.$type.'/'.$table.self::$file_ext) )
		{
			self::$display_name = $type.'/'.$table.self::$file_ext;
			self::$display_prefix     = 'content\\';
		}
		// elseif template type with name of table exist show it
		elseif( is_file(root.'content/template/'.$type.'-'.$table.self::$file_ext) )
		{
			self::$display_name	= $type.'-'.$table.self::$file_ext;
		}
		// elseif template type exist show it like posts or terms
		elseif( is_file(root.'content/template/'.$type.self::$file_ext) )
		{
			self::$display_name	= $type.self::$file_ext;
		}
		// elseif template cat exist show it
		// elseif( is_file(root.'content/template/'.$post_cat.self::$file_ext) )
		// {
		// 	self::$display_name	= $post_cat.self::$file_ext;
		// }

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$table.self::$file_ext) )
		{
			self::$display_name	= $myurl['table'].self::$file_ext;
		}

		// elseif default template exist show it else use homepage!
		elseif( is_file(root.'content/template/dafault'. self::$file_ext) )
		{
			self::$display_name	= 'dafault'. self::$file_ext;
		}
		// if find template for this url
		// then if template for current lang is exist, set it
		if(self::$display_name)
		{

			self::$display_name    = self::$display_prefix. self::$display_name;
			$current_lang          = \lib\define::get_language('name');

			$current_lang_template = substr(self::$display_name, 0, -(strlen(self::$file_ext)));
			$current_lang_template .= '-'.$current_lang . self::$file_ext;

			$current_lang_template = str_replace("\\", DIRECTORY_SEPARATOR, $current_lang_template);
			$current_lang_template = str_replace("/", DIRECTORY_SEPARATOR, $current_lang_template);

			if(is_file(root.$current_lang_template))
			{
				self::$display_name	= $current_lang_template;
			}
			$route_check_true = true;
		}

		if(isset($data['meta']) && is_string($data['meta']) && substr($data['meta'], 0,1) === '{')
		{
			$data['meta'] = json_decode($data['meta'], true);
		}

		if($route_check_true)
		{
			self::$datarow = $data;
			self::$route_check_true = $route_check_true;
		}
	}


	public static function social_short_link()
	{
		// save name of current module as name of social
		$mymodule    = self::$module;
		$social_name = $mymodule;

		if(\lib\option::social('status'))
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
			\lib\option::social($social_name, 'name') &&
			is_string(\lib\option::social($social_name, 'name'))
		  )
		{
			// create url of social network
			$social_url = $social_list[$social_name] . \lib\option::social($social_name, 'name');
			// redirect to new address
			(new \lib\redirector($social_url, false))->redirect();
			return true;
		}

		return false;
	}


	public static function fake_static_page()
	{
		$mymodule    = self::$module;

		// if user entered url contain one of our site language
		$current_path = \lib\url::dir();
		if(is_array($current_path))
		{
			$current_path = implode('/', $current_path);
		}

		// if custom template exist show this template
		if( is_file(root.'content/template/static_'. $current_path. self::$file_ext) )
		{
			self::$display_name = 'static_'. $current_path. self::$file_ext;

		}
		elseif( is_file(root.'content/template/static/'. $current_path. self::$file_ext) )
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
			if(is_file(root.'content/template/static/'. $my_special_url. self::$file_ext))
			{
				self::$display_name = 'static\\'. $my_special_url. self::$file_ext;

			}
		}

		if(self::$display_name)
		{

			self::$display_name    = self::$display_prefix. self::$display_name;
			$current_lang          = \lib\define::get_language('name');

			$current_lang_template = substr(self::$display_name, 0, -(strlen(self::$file_ext)));
			$current_lang_template .= '-'.$current_lang . self::$file_ext;

			$current_lang_template = str_replace("\\", DIRECTORY_SEPARATOR, $current_lang_template);
			$current_lang_template = str_replace("/", DIRECTORY_SEPARATOR, $current_lang_template);

			if(is_file(root.$current_lang_template))
			{
				self::$display_name	= $current_lang_template;
			}
			return true;
		}

		return false;

	}


	private static function get_my_url()
	{
		$myUrl = \lib\router::get_url();
		$myUrl = \lib\router::urlfilterer($myUrl);
		return $myUrl;
	}


	public static function find_cat()
	{
		$myUrl = self::get_my_url();

		if(substr($myUrl, 0, 9) === 'category/')
		{
			$cat_data = \lib\db\terms::get(['url' => substr($myUrl, 9), 'limit' => 1]);
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
			$cat_data = \lib\db\terms::get(['url' => substr($myUrl, 4), 'limit' => 1]);
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

		$term_data = \lib\db\terms::get(['url' => $myUrl, 'type' => 'cat', 'limit' => 1]);

		if($term_data)
		{
			return $term_data;
		}

		$term_data = \lib\db\terms::get(['url' => $myUrl, 'type' => 'tag', 'limit' => 1]);

		if($term_data)
		{
			return $term_data;
		}
		return false;
	}


	public static function find_post()
	{
		if(!empty(db_name))
		{
			$post_detail = \lib\app\posts::find_post();
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
			header("HTTP/1.1 404 NOT FOUND");
			self::$display_name	= '404.html';
			return true;
		}
		// else show dash default error page
		else
		{
			\lib\error::page(T_("Does not exist!"));
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