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

		// set url values
		$this->url               = \lib\url::all();
		$this->url['static']     = \lib\url::site(). '/static/';
		$this->url['repository'] = 'site';
		if(\lib\url::content())
		{
			$this->url['repository'] = \lib\url::content();
		}

		// return all parameters and clean it
		$this->data->utilityGET = \lib\request::get(null, 'raw');


		$this->data->site['title']       = T_("Ermile Dash");
		$this->data->site['desc']        = T_("Another Project with Ermile dash");
		$this->data->site['slogan']      = T_("Ermile is intelligent ;)");
		$this->data->site['langlist']    = \lib\language::list(true);
		$this->data->site['currentlang'] = \lib\language::current();
		$this->data->site['defaultLang'] = \lib\language::default();

		// save all options to use in display
		$this->data->options = \lib\option::config();

		$this->data->page['title']   = null;
		$this->data->page['desc']    = null;
		$this->data->page['special'] = null;
		$this->data->bodyclass       = null;

		$this->data->login           = \lib\user::login('all');
		$this->data->user            = \lib\user::detail();
		// $this->data->perm            = $this->access(null, 'all');
		// $this->data->permContent     = $this->access('all');

		// set detail of browser
		$this->data->browser         = \lib\utility\browserDetection::browser_detection('full_assoc');
		$this->data->visitor         = 'not ready!';

		// define default value for global
		$this->global->title         = null;
		$this->global->login         = \lib\user::login();

		$this->global->lang          = $this->data->site['currentlang'];
		$this->global->direction     = \lib\language::current('direction');
		$this->global->id            = implode('_', \lib\url::dir());

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

		$this->data->url = $this->url;

		$this->data->dev = \lib\option::config('dev');

		// if allow to use social then get social network account list
		if(\lib\option::social('status'))
		{
			$this->data->social = \lib\option::social('list');

			// create data of share url
			$this->data->share['title']       = $this->data->site['title'];
			$this->data->share['desc']        = $this->data->site['desc'];
			$this->data->share['image']       = $this->url['static']. 'images/logo.png';
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
}
?>