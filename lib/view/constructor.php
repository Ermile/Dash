<?php
namespace lib\view;

trait constructor
{
	/**
	 * [mvc_construct description]
	 * @return [type] [description]
	 */
	public function mvc_construct()
	{
		array_push($this->twig_include_path, addons);

		// define default value for url
		$this->url->full             = \lib\url::pwd();       // full url except get parameter with http[s]
		$this->url->path             = $this->url('path');       // full path except parameter and domain name
		$this->url->breadcrumb       = $this->url('breadcrumb'); // full path in array for using in breadcrumb
		$this->url->domain           = $this->url('domain');     // domain name like 'ermile'
		$this->url->base             = $this->url('base');
		$this->url->baseRaw          = rtrim($this->url('baseRaw'), '/') . '/';
		$this->url->prefix           = $this->url('prefix');
		$this->url->content          = $this->url('content');
		$this->url->baseContent      = $this->url('baseContent');
		$this->url->baseFull         = $this->url('baseFull');
		$this->url->tld              = $this->url('tld');        // domain ltd like 'com'
		$this->url->raw              = \lib\url::domain();                  // domain name except subdomain like 'ermile.com'
		$this->url->root             = $this->url('root');
		$this->url->static           = $this->url->root. '/'.'static/';
		$this->url->protocol         = \lib\url::protocol();
		$this->url->account          = $this->url('account');
		$this->url->MainStatic       = $this->url('MainService'). '/'.'static/';
		$this->url->MainSite         = $this->url('MainSite');
		$this->url->MainProtocol     = $this->url('MainProtocol');
		$this->url->SubDomain        = \lib\url::subdomain()? \lib\url::subdomain().'.': null;
		$this->url->repository       = \lib\router::get_repository_name();
		if($this->url->repository === 'content')
		{
			$this->url->repository = 'site';
		}
		else
		{
			$this->url->repository = str_replace('content_', '', $this->url->repository);
		}

		// return all parameters and clean it
		$this->url->param       = \lib\utility::get(null, true);
		$this->data->utilityGET = \lib\utility::get(null, 'raw');
		$this->url->all         = $this->url->full.$this->url->param;

		$this->data->site['title']       = T_("Ermile Dash");
		$this->data->site['desc']        = T_("Another Project with Ermile dash");
		$this->data->site['slogan']      = T_("Ermile is intelligent ;)");
		$this->data->site['langlist']    = \lib\language::list();
		$this->data->site['currentlang'] = \lib\language::get_language();
		$this->data->site['defaultLang'] = \lib\language::get_language('default');

		// save all options to use in display
		$this->data->options = \lib\option::config();

		$this->data->page['title']   = null;
		$this->data->page['desc']    = null;
		$this->data->page['special'] = null;
		$this->data->bodyclass       = null;
		$this->data->module          = $this->module();
		$this->data->modulePath      = $this->url('baseFull'). '/'. $this->module();
		$this->data->child           = $this->child();
		$this->data->login           = $this->login('all');
		$this->data->user            = \lib\user::detail();
		// $this->data->perm            = $this->access(null, 'all');
		// $this->data->permContent     = $this->access('all');

		// set detail of browser
		$this->data->browser         = \lib\utility\browserDetection::browser_detection('full_assoc');
		$this->data->visitor         = 'not ready!';

		// define default value for global
		$this->global->title         = null;
		$this->global->login         = $this->login();

		$this->global->lang          = $this->data->site['currentlang'];
		$this->global->direction     = \lib\language::get_language('direction');
		$this->global->id            = $this->url('path','_');

		// add special pages to display array to use without name
		$this->data->display['main']       = "content/main/layout.html";
		$this->data->display['home']       = "content/home/display.html";
		$this->data->display['account']    = "content_account/home/layout.html";
		$this->data->display['cp']         = "content_cp/home/layout.html";
		$this->data->display['su']         = "content_su/home/layout.html";
		$this->data->display['cpMain']     = "content_cp/main/layout.html";
		$this->data->display['suMain']     = "content_su/main/layout.html";
		$this->data->display['pagination'] = "includes/html/inc_pagination.html";
		// add special pages to template array to use without name
		$this->data->template['header']    = 'content/template/header.html';
		$this->data->template['sidebar']   = 'content/template/sidebar.html';
		$this->data->template['footer']    = 'content/template/footer.html';


		$this->data->dev = \lib\option::config('dev');

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			$this->data->social = \lib\option::social('list');

			// create data of share url
			$this->data->share['title']       = $this->data->site['title'];
			$this->data->share['desc']        = $this->data->site['desc'];
			$this->data->share['image']       = $this->url->static. 'images/logo.png';
			$this->data->share['twitterCard'] = 'summary';
		}

		// define default value for include
		$this->include->newline      = PHP_EOL;
		$this->include->css_ermile   = false;
		$this->include->js_main      = false;
		$this->include->siftal       = true;
		$this->include->css          = true;
		$this->include->js           = true;
		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(isset($this->url->MainStatic) && $this->url->MainStatic)
		{
			$this->url->myStatic = $this->url->MainStatic;
		}
		elseif(isset($this->url->MainStatic))
		{
			$this->url->myStatic = $this->url->static;
		}


		// we offer 3 type of function to be used in order to have some change on module
		// you can call this all time needed, but Recomended to call on project mvc view
		if(method_exists($this, 'project'))
		{
			$this->project();
		}
		// like project but recomend to call on repository
		if(method_exists($this, 'repository'))
		{
			$this->repository();
		}
		// like project but recomend to call on special module
		if(method_exists($this, 'config'))
		{
			$this->config();
		}
	}


	/**
	 * [save_as_cookie description]
	 * @return [type] [description]
	 */
	public function save_as_cookie()
	{
		if(\lib\option::config('save_as_cookie'))
		{
			$mygetlist = \lib\utility::get(null, 'raw');
			if($mygetlist)
			{
				foreach ($mygetlist as $name => $value)
				{
					if($name === 'ssid')
					{
						$_SESSION['ssid'] = $value;
					}
					elseif( !($name === 'local' || $name === 'lang') )
					{
						\lib\utility\cookie::write($name, $value);
					}
				}

				// remove get parameter from url
				header('Location: '. \lib\url::pwd());
			}
		}
	}
}
?>