<?php
namespace addons\content_cp\home;

class view extends \mvc\view
{
	public function config()
	{
		// $this->data->list             = $this->cpModlueList('all');
		$this->data->bodyclass        = 'fixed unselectable';
		$this->include->css_ermile    = true;
		$this->include->css           = false;
		$this->include->js            = false;
		$this->include->fontawesome   = true;
		$this->include->datatable     = true;
		$this->include->chart         = true;
		$this->include->introjs       = true;
		$this->include->lightbox      = true;
		$this->include->editor        = true;
		$this->include->cp            = true;
		$this->include->uploader      = true;
		$this->global->js             = [];

		$this->data->display['cp_posts'] = "content_cp/posts/layout.html";;


		$this->data->saloos['version']    = \lib\saloos::getLastVersion();
		$this->data->saloos['lastUpdate'] = \lib\saloos::getLastUpdate();
		$this->data->saloos['langlist']   = ['fa_IR' => 'Persian - فارسی',
											 'en_US' => 'English',
											 'ar_SU' => 'Arabic - العربية'];

		$this->data->modules 		  = $this->controller::$manifest['modules']->get_modules();
		// $this->global->js             = [$this->url->myStatic.'js/highcharts/highcharts.js'];
		// $this->data->page['desc']  = 'salam';
		$mymodule = $this->module();
		$this->data->page['desc']	  = $this->controller::$manifest['modules']->get_modules($mymodule, "desc");
		$this->data->page['title']	  = $this->controller::$manifest['modules']->get_modules($mymodule, "title");

		$this->data->page['haschild'] = $this->controller::$manifest['modules']->get_modules($mymodule, "childless") ? false : true;
		$this->data->page['title']    = T_(ucfirst(\lib\router::get_url(' ')));

		$this->data->cpModule         = $this->cpModule();

		$this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		$this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';

		switch ($mymodule)
		{
			case 'visitors':
				if(\lib\option::config('log_visitors'))
				{
					// create for chart
					$type  = \lib\utility::get('type');
					$utype = \lib\utility::get('utype');
					$stype = \lib\utility::get('stype');
					$atype = \lib\utility::get('atype');
					$this->data->chart_type             = $type?  $type:  'column';
					$this->data->chart_unique_type      = $utype? $utype: 'areaspline';
					$this->data->chart_signup_type      = $stype? $stype: 'areaspline';
					$this->data->chart_answered_type    = $atype? $atype: 'column';

					// $this->data->visitors               = $this->model()->visitors();
					// $this->data->visitors_unique        = $this->model()->visitors(true);

					$this->data->visitors               = \lib\utility\visitor::chart();
					$this->data->visitors_unique        = \lib\utility\visitor::chart(true);

					// get period of signup from user
					$this->data->period = \lib\utility::get('period');
					switch ($this->data->period)
					{
						case 'year':
							$period = "%Y";
							break;

						case 'month':
							$period = "%Y-%m";
							break;

						case 'week':
							$period = "%Y ". T_('week')."%V";
							break;

						case 'day':
						default:
							$period = "%Y-%m-%d";
							break;
					}
					$this->data->signup   = \lib\db\chart\users::signup($period);
					if(class_exists('\lib\db\chart\polls'))
					{
						$this->data->answered = \lib\db\chart\polls::answeredCount($period);
					}

					if($this->data->visitors <= 1)
					{
						$this->data->error = T_("Chart must be contain at least 2 column!");
					}
				}
				break;

			case 'home':
				$this->data->countOf['posts']       = $this->model()->countOf('posts');
				$this->data->countOf['pages']       = $this->model()->countOf('pages');
				$this->data->countOf['attachments'] = $this->model()->countOf('attachments');
				$this->data->countOf['books']       = $this->model()->countOf('books');
				$this->data->countOf['tags']        = $this->model()->countOf('tags');
				$this->data->countOf['categories']  = $this->model()->countOf('categories');
				$this->data->countOf['users']       = $this->model()->countOf('users');

				$this->data->bodyclass              .= ' unselectable';
				// check visitor is new or not
				$this->data->visitor_new            = false;
				$ref = \lib\router::urlParser('referer', 'sub');
				if($ref !== 'cp' && $ref !== null)
					$this->data->visitor_new = true;

				if(\lib\option::config('log_visitors'))
				{
					// create for chart
					$this->data->chart_type             = 'column';
					$this->data->visitors               = \lib\utility\visitor::chart();
					$this->data->visitors_toppages      = \lib\utility\visitor::top_pages(15);

					if($this->data->visitors <= 1)
					{
						$this->data->error = T_("Chart must be contain at least 2 column!");
					}
				}

				break;

			default:
				# code...
				break;
		}

		if($this->data->page['haschild'])
		{
			// Check permission and if user can do this operation
			// allow to do it, else show related message in notify center
			$myResult = $this->access('cp', $mymodule, 'add');
			$this->data->page['haschild'] = $myResult? true: false;
		}
		// $f = array_keys($this->controller::modules_hasnot('disable'));
		// $feature = [];
		// foreach ($f as $key => $value) {
		// 	$feature[$value] = true;
		// }
		// $this->data->site['title']  = T_('Control Panel'). ' - ' . $this->data->site['title'];
	}

	function view_datatable()
	{
		// in root page like site.com/admin/banks show datatable
		// get data from database through model
		switch ($this->module())
		{
			case 'visitors':
				break;
			case 'profile':
				$this->data->datarow = $this->model()->datarow('users', $this->login('id'));
				break;

			default:
				$this->data->datatable = $this->model()->datatable();
				break;
		}
	}


	public function view_child()
	{
		$mytable                = $this->cpModule('table');
		$mychild                = $this->child();
		// $this->global->js       = array($this->url->myStatic.'js/cp/medium-editor.min.js');
		$this->data->enum       = \lib\sql\getTable::enumValues('posts');

		switch ($mytable)
		{
			case 'posts':
				// show list of tags
				$this->data->tagList = $this->model()->sp_term_list();

				// for each type of post
				switch ($this->cpModule('raw'))
				{
					case 'pages':
						$this->data->parentList = $this->model()->sp_parent_list();
						break;

					case 'helps':
						$this->data->parentList = $this->model()->sp_parent_list(true, 'help');
						$this->data->catList    = $this->model()->sp_cats('cat_help');
						break;

					case 'polls':
						$this->data->parentList = $this->model()->sp_parent_list(true, 'poll');
						$this->data->catList = $this->model()->sp_cats('cat_poll');
						break;

					case 'attachments':
						$this->data->maxSize = \lib\utility\upload::max_file_upload_in_bytes();
						// $this->include->uploader      = true;
						// array_push($this->global->js, $this->url->myStatic.'js/cp/uploader.js');
						$this->data->catList = $this->model()->sp_cats('filecat');
						$this->data->catListSelected = $this->model()->sp_cats('cat_file', true);
						break;

					case 'books':
						$this->data->catList = $this->model()->sp_cats('bookcat');
						$this->data->catListSelected = $this->model()->sp_cats('cat_book', true);
						$this->data->parentList = $this->model()->sp_parent_list(true, 'book');

						break;

					case 'socialnetwork':
						$this->data->catList = null;

						break;

					default:
						$this->data->catList = $this->model()->sp_cats();
						break;
				}
				break;

			default:
				switch ($this->cpModule('raw'))
				{
					case 'categories':
					case 'helpcategories':
					case 'pollcategories':
					case 'filecategories':
					case 'bookcategories':
						$this->data->parentList = $this->model()->sp_category_list($this->cpModule('type'));
						break;
				}

				$this->data->field_list = \lib\sql\getTable::get($mytable);
				$myform = $this->createform('@'.db_name.'.'.$mytable, $this->data->child);
				break;
		}

		// if module for users then fill permission list
		if($this->cpModule('raw') === 'users')
		{
			$this->draw_users();
		}


		if($mychild === 'edit')
		{
			$this->data->datarow = $this->model()->datarow($mytable, null, true);
			// set shortURL
			$this->data->shortURL = 'sp_'. \lib\utility\shortURL::encode($this->data->datarow['id']);

			if(isset($this->data->datarow['post_meta']))
				$this->data->datarow['post_meta'] = json_decode($this->data->datarow['post_meta'], true);

			if($this->cpModule('raw') === 'attachments')
			{
				if(isset($this->data->datarow['meta']['slug']))
					$this->data->datarow['post_slug'] = $this->data->datarow['meta']['slug'];
			}


			if($mytable === 'posts')
			{
				// $this->data->datarow['post_content'] .= '<img src="/static/images/logo.png" />';
				// var_dump($this->data->datarow['post_content']);
				$url = $this->data->datarow['post_url'];
				$this->data->datarow['cat_url'] = substr($url, 0, strrpos( $url, '/'));
				// if defaultlang and lang of the post is not the same then add lang to url
				$defaultLang = substr(\lib\option::config('default_language'), 0, 2);
				if($defaultLang !== $this->data->datarow['post_language'])
				{
					$this->data->datarow['post_url'] = $this->data->datarow['post_language']. "/".$this->data->datarow['post_url'];
				}
			}
		}
	}

	function draw_users()
	{
		$mychild                = $this->child();

		$this->data->form->users->user_status->required('required');

		$checkStatus = null;
		$myPerm      = null;
		$myPermNames = \lib\utility\option::permList();
		$myPermList  = $this->data->form->users->user_permission;
		if(count($myPermNames) > 5)
		{
			$myPermList->type('select');
			$checkStatus = 'selected';
		}
		else
		{
			$myPermList->type('radio');
			$checkStatus = 'checked';
		}
		if($mychild === 'edit')
		{
			$myPerm = $this->model()->datarow('users');
			$myPerm = $myPerm['user_permission'];
			if($myPerm === "1")
			{
				$myPermList->addClass('hide');
			}
		}


		// get list of permissions
		foreach ($myPermNames as $key => $value)
		{

			if($myPerm == $key)
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm'.$key)->$checkStatus();
			}
			else
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm'.$key);
			}
		}
		$this->data->form->users->user_pass->autocomplete('new-password');
		$myPass = $this->data->form->users->user_pass;
		if($mychild === 'add')
		{
			$myPass->attr('required', 'required')->pl(T_('Enter password within 5 to 40 character') );
		}
		else
		{
			$myPass->label(T_('New Password'))->value(null)->pl(T_('If you want to change password enter it, else leave it blank'));
		}
		// get user position and set it
		$position = null;
		if(isset($this->data->form->users->datarow['user_meta']))
		{
			$meta = $this->data->form->users->datarow['user_meta'];
			$meta = json_decode($meta, true);
			if(isset($meta['position']))
			{
				$position = $meta['position'];
			}
		}
		$this->data->form->users->add('position', 'text')->name('position')->label(T_("position"))->value($position)->compile();
		$this->data->form->users->after('position', 'user_displayname');
	}

	public function addons_config($_name, $_addons){
		$addons_path = $_addons['path'];
		$config_addons = array();
		$addons_valid_display = ['panel'];
		foreach ($addons_valid_display as $key => $value) {
			$display = join(DIRECTORY_SEPARATOR, [$addons_path, $value . '.html']);
			if($this->twig_file_exists($display))
			{
				$config_addons[$value] = $display;
			}
		}
		if(!isset($this->data->addons))
		{
			$this->data->addons = array();
		}
		$this->data->addons[$_name] = $config_addons;
	}

	// function pushState()
	// {
	// 	// temporary disable push state on control panel
	// 	$this->data->display['cp'] = null;
	// }
}
?>