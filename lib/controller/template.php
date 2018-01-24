<?php
namespace lib\controller;

trait template
{

	public function d_template_finder()
	{
		// finded the social short link
		if($this->social_short_link())
		{
			// redirect
			return;
		}

		if($this->fake_static_page())
		{
			return true;
		}

		$data  = null;
		$slug  = null;
		$type  = null;
		$table = null;

		if($data = $this->find_cat())
		{
			$type  = 'category';
			$table = 'terms';
			if(isset($data['slug']))
			{
				$slug = $data['slug'];
			}
		}
		elseif($data = $this->find_tag())
		{
			$type  = 'tag';
			$table = 'terms';
			if(isset($data['slug']))
			{
				$slug = $data['slug'];
			}
		}
		elseif($data = $this->find_post())
		{
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
		elseif($this->find_404())
		{
			// no way to load page
			return;
			// :(
		}

		$this->set_display_name($data, $type, $slug, $table);

	}


	public function set_display_name($data, $type, $slug, $table)
	{
		$file_ext         = '.html';
		$display_prefix   = 'content\template\\';
		$route_check_true = false;


		// elseif template type with specefic slug exist show it
		if( is_file(root.'content/template/'.$type.'-'. $slug. $file_ext) )
		{
			$this->display_name	= $type.'-'.$slug. $file_ext;
		}
		// elseif template type with name of table exist in module folder then show it
		elseif( is_file(root.'content/'.$type.'/'.$table.$file_ext) )
		{
			$this->display_name = $type.'/'.$table.$file_ext;
			$display_prefix     = 'content\\';
		}
		// elseif template type with name of table exist show it
		elseif( is_file(root.'content/template/'.$type.'-'.$table.$file_ext) )
		{
			$this->display_name	= $type.'-'.$table.$file_ext;
		}
		// elseif template type exist show it like posts or terms
		elseif( is_file(root.'content/template/'.$type.$file_ext) )
		{
			$this->display_name	= $type.$file_ext;
		}
		// elseif template cat exist show it
		// elseif( is_file(root.'content/template/'.$post_cat.$file_ext) )
		// {
		// 	$this->display_name	= $post_cat.$file_ext;
		// }

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$table.$file_ext) )
		{
			$this->display_name	= $myurl['table'].$file_ext;
		}

		// elseif default template exist show it else use homepage!
		elseif( is_file(root.'content/template/dafault'. $file_ext) )
		{
			$this->display_name	= 'dafault'. $file_ext;
		}
		// if find template for this url
		// then if template for current lang is exist, set it
		if($this->display_name)
		{

			$this->display_name    = $display_prefix. $this->display_name;
			$current_lang          = \lib\define::get_language('name');
			$current_lang_template = substr($this->display_name, 0, -(strlen($file_ext)));
			$current_lang_template .= '-'.$current_lang . $file_ext;

			$current_lang_template = str_replace("\\", DIRECTORY_SEPARATOR, $current_lang_template);
			$current_lang_template = str_replace("/", DIRECTORY_SEPARATOR, $current_lang_template);

			if(is_file(root.$current_lang_template))
			{
				$this->display_name	= $current_lang_template;
			}
			$route_check_true = true;
		}

		if($route_check_true)
		{
			$this->datarow = $data;
			$this->route_check_true = $route_check_true;
			$this->get()->ALL();
		}
	}


	public function social_short_link()
	{
		// save name of current module as name of social
		$mymodule    = $this->module();
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
			$this->redirector($social_url, false)->redirect();
			return true;
		}

		return false;
	}


	public function fake_static_page()
	{
		$mymodule    = $this->module();
		$file_ext         = '.html';
		$display_prefix   = 'content\template\\';

		// if user entered url contain one of our site language
		$current_path = $this->url('path', '_');
		// if custom template exist show this template
		if( is_file(root.'content/template/static_'. $current_path. $file_ext) )
		{
			$this->display_name = 'static_'. $current_path. $file_ext;
			return true;
		}
		elseif( is_file(root.'content/template/static/'. $current_path. $file_ext) )
		{
			$this->display_name = 'static\\'. $current_path. $file_ext;
			return true;
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
			if(is_file(root.'content/template/static/'. $my_special_url. $file_ext))
			{
				$this->display_name = 'static\\'. $my_special_url. $file_ext;
				return true;
			}
		}

		return false;
	}


	public function find_cat()
	{
		$myUrl = \lib\router::get_url();

		$myUrl = \lib\router::urlfilterer($myUrl);

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


	public function find_tag()
	{
		$myUrl = \lib\router::get_url();

		$myUrl = \lib\router::urlfilterer($myUrl);

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


	public function find_post()
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


	public function find_404()
	{
		if( is_file(root.'content/template/404.html') )
		{
			header("HTTP/1.1 404 NOT FOUND");
			$this->display_name	= '404.html';
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
	 * [s_template_finder description]
	 * @return [type] [description]
	 */
	public function s_template_finder()
	{
		// if lang exist in module or subdomain remove it and continue
		$currentLang = \lib\define::get_language();
		$defaultLang = substr(\lib\define::get_language('default'), 0, 2);

		if($currentLang === SubDomain && $currentLang !== $defaultLang)
		{
			\lib\router::set_sub_domain(null);
		}
		// elseif($currentLang === $this->module() && $currentLang !== $defaultLang)
		// 	\lib\router::remove_url($currentLang);



		// continue find best template for this condition
		$mymodule    = $this->module();
		if($mymodule == 'home')
		{
			// if home template exist show it
			if( is_file(root.'content/template/home.html') )
				$this->display_name	= 'content\template\home.html';
			$this->get()->ALL();
			return 0;
		}


		elseif($mymodule == 'search')
		{
			if( is_file(root.'content/template/search.html') )
				$this->display_name	= 'content\template\search.html';

			$this->get()->ALL();
			return;
		}


		elseif($mymodule == 'feed')
		{
			$site_title    = $this->view()->data->site['title'];
			$site_desc     = $this->view()->data->site['desc'];
			$site_protocol = $this->url('MainProtocol'). '://';
			$site_url      = $this->url('MainSite');

			$rss = new \lib\utility\RSS($site_protocol, $site_url, $site_title, $site_desc);
			// add posts
			foreach ($this->model()->get_feeds() as $row)
				$rss->addItem($row['link'], $row['title'], $row['desc'], $row['date']);

			$rss->create();
			return;
		}
		else
		{
			// save name of current module as name of social
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
				$this->redirector($social_url, false)->redirect();
				return;
			}
		}

		$myurl = null;
		$route_check_true = false;

		if(!empty(db_name))
		{
			$post_detail = \lib\app\posts::find_post();
			if($post_detail)
			{
				$route_check_true = true;
			}
		}

		// set post type, get before underscope
		$post_type = null;
		if(isset($myurl['type']))
		{
			$post_type = strtok($myurl['type'], '_');
		}
		$post_cat = null;
		if(isset($myurl['cat']))
		{
			$post_cat = $myurl['cat'];
			$post_cat = str_replace('/', '_', $post_cat);
			if($myurl['table'] === 'terms')
			{
				$post_cat .= '_home';
			}
		}

		$file_ext         = '.html';
		$display_prefix   = 'content\template\\';
		// if url does not exist show 404 error
		if(!$myurl)
		{
			// if user entered url contain one of our site language
			$current_path = $this->url('path', '_');
			// if custom template exist show this template
			if( is_file(root.'content/template/static_'. $current_path. $file_ext) )
			{
				$this->display_name = 'static_'. $current_path. $file_ext;
			}
			elseif( is_file(root.'content/template/static/'. $current_path. $file_ext) )
			{
				$this->display_name = 'static\\'. $current_path. $file_ext;
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
				if(is_file(root.'content/template/static/'. $my_special_url. $file_ext))
				{
					$this->display_name = 'static\\'. $my_special_url. $file_ext;
				}
			}
			// // elseif 404 template exist show it
			// elseif( is_file(root.'content/template/404.html') )
			// {
			// 	header("HTTP/1.1 404 NOT FOUND");
			// 	$this->display_name	= '404.html';
			// }
			// // else show dash default error page
			// else
			// {
			// 	\lib\error::page(T_("Does not exist!"));
			// 	return;
			// }
		}

		// elseif template type with specefic slug exist show it
		elseif( is_file(root.'content/template/'.$post_type.'-'.$myurl['slug'].$file_ext) )
		{
			$this->display_name	= $post_type.'-'.$myurl['slug'].$file_ext;
		}
		// elseif template type with name of table exist in module folder then show it
		elseif( is_file(root.'content/'.$post_type.'/'.$myurl['table'].$file_ext) )
		{
			$this->display_name = $post_type.'/'.$myurl['table'].$file_ext;
			$display_prefix     = 'content\\';
		}
		// elseif template type with name of table exist show it
		elseif( is_file(root.'content/template/'.$post_type.'-'.$myurl['table'].$file_ext) )
		{
			$this->display_name	= $post_type.'-'.$myurl['table'].$file_ext;
		}
		// elseif template type exist show it like posts or terms
		elseif( is_file(root.'content/template/'.$post_type.$file_ext) )
		{
			$this->display_name	= $post_type.$file_ext;
		}
		// elseif template cat exist show it
		elseif( is_file(root.'content/template/'.$post_cat.$file_ext) )
		{
			$this->display_name	= $post_cat.$file_ext;
		}

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$myurl['table'].$file_ext) )
		{
			$this->display_name	= $myurl['table'].$file_ext;
		}

		// elseif default template exist show it else use homepage!
		elseif( is_file(root.'content/template/dafault'. $file_ext) )
		{
			$this->display_name	= 'dafault'. $file_ext;
		}
		// if find template for this url
		// then if template for current lang is exist, set it
		if($this->display_name)
		{

			$this->display_name    = $display_prefix. $this->display_name;
			$current_lang          = \lib\define::get_language('name');
			$current_lang_template = substr($this->display_name, 0, -(strlen($file_ext)));
			$current_lang_template .= '-'.$current_lang . $file_ext;

			$current_lang_template = str_replace("\\", DIRECTORY_SEPARATOR, $current_lang_template);
			$current_lang_template = str_replace("/", DIRECTORY_SEPARATOR, $current_lang_template);

			if(is_file(root.$current_lang_template))
			{
				$this->display_name	= $current_lang_template;
			}
			$route_check_true = true;
		}


		if($route_check_true)
		{
			$this->route_check_true = $route_check_true;
			$this->get(null, $myurl['table'])->ALL("/.*/");
		}
	}
}
?>