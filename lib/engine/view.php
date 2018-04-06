<?php
namespace dash\engine;

class view
{

	public static function variable()
	{
		// default display value
		\lib\data::display_mvc("includes/html/display-mvc.html");
		\lib\data::display_dash("includes/html/display-dash.html");
		\lib\data::display_enter("includes/html/display-enter.html");
		// add special pages to display array to use without name
		\lib\data::display_main("content/main/layout.html");
		\lib\data::display_account("content_account/home/layout.html");
		\lib\data::display_cpMain("content_cp/main/layout.html");
		\lib\data::display_suMain("content_su/main/layout.html");

		\lib\data::display_pagination("includes/html/inc_pagination.html");

		// return all url detail
		\lib\data::url(\lib\url::all());

		// return all parameters and clean it
		\lib\data::requestGET(\lib\request::get());

		// ----- language variable
		\lib\data::lang_list(\lib\language::list(true));
		\lib\data::lang_current(\lib\language::current());
		\lib\data::lang_default(\lib\language::default());

		// save all options to use in display
		\lib\data::options(\lib\option::config());

		\lib\data::page_title(null);
		\lib\data::page_desc(null);
		\lib\data::page_special(null);

		\lib\data::bodyclass(null);

		$user_detail = \lib\user::detail();
		\lib\data::user($user_detail);
		\lib\data::login($user_detail);

		// set detail of browser
		\lib\data::browser(\lib\utility\browserDetection::browser_detection('full_assoc'));
		\lib\data::visitor('not ready!');

		// define default value for global
		\lib\data::global_title(null);
		\lib\data::global_login(\lib\user::login());
		\lib\data::global_lang(\lib\language::current());
		\lib\data::global_direction(\lib\language::current('direction'));
		\lib\data::global_id(implode('_', \lib\url::dir()));

		\lib\data::dev(\lib\option::config('dev'));

		\lib\data::site_title(T_("Ermile Dash"));
		\lib\data::site_desc(T_("Another Project with Ermile dash"));
		\lib\data::site_slogan(T_("Ermile is intelligent ;)"));

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			\lib\data::social(\lib\option::social('list'));
			// create data of share url
			\lib\data::share_title(\lib\data::get('site', 'title'));
			\lib\data::share_desc(\lib\data::get('site', 'desc'));
			\lib\data::share_image(\lib\url::site(). '/static/images/logo.png');
			\lib\data::share_twitterCard('summary');
		}

		// define default value for include
		\lib\data::include_siftal(true);
		\lib\data::include_css(true);
		\lib\data::include_js(true);

		\lib\data::pagination(\lib\utility\pagination::page_number());
	}


	/**
	 * set title for pages depending on condition
	 */
	public static function set_title()
	{
		if($page_title = \lib\data::page_title())
		{
			// set title of locations if exist in breadcrumb
			if(\lib\data::get('breadcrumb', $page_title))
			{
				$page_title = \lib\data::get('breadcrumb', $page_title);
			}
			// replace title of page
			if(!\lib\data::page_special())
			{
				$page_title = ucwords(str_replace('-', ' ', $page_title));
			}
			// for child page set the
			if(\lib\url::child() && \lib\url::subdomain() === 'cp')
			{
				$myModule = \lib\url::module();
				if(substr($myModule, -3) === 'ies')
				{
					$moduleName = substr($myModule, 0, -3).'y';
				}
				elseif(substr($myModule, -1) === 's')
				{
					$moduleName = substr($myModule, 0, -1);
				}
				else
				{
					$moduleName = $myModule;
				}

				$childName = \lib\url::child();
				if($childName)
				{
					$page_title = T_($childName).' '.T_($moduleName);
				}
			}

			// translate all title at last step
			$page_title = T_($page_title);

			\lib\data::page_title($page_title);

			if(\lib\data::page_special())
			{
				\lib\data::global_title($page_title);
			}
			else
			{
				\lib\data::global_title($page_title.' | '.T_(\lib\data::site_title()));
			}
		}
		else
		{
			\lib\data::global_title(T_(\lib\data::site_title()));
		}

		\lib\data::global_short_title(substr(\lib\data::global_title(), 0, strrpos(substr(\lib\data::global_title(), 0, 120), ' ')). '...');
	}


	public static function set_cms_titles()
	{
		if(!\lib\data::get('datarow'))
		{
			return false;
		}

		// set title
		if(\lib\data::datarow_title())
		{
			\lib\data::page_title(\lib\data::datarow_title());
		}

		// set desc
		if(\lib\data::datarow_excerpt())
		{
			\lib\data::page_desc(\lib\data::datarow_excerpt());
		}
		elseif(\lib\data::datarow_content())
		{
			\lib\data::page_desc(\lib\utility\excerpt::extractRelevant(\lib\data::datarow_content()));
		}
		elseif(\lib\data::datarow_desc())
		{
			\lib\data::page_desc(\lib\utility\excerpt::extractRelevant(\lib\data::datarow_desc()));
		}

		// set new title
		self::set_title();
	}
}
?>
