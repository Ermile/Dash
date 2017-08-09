<?php
namespace addons\content_cp\options;

class view extends \addons\content_cp\home\view
{
	public function config()
	{
		parent::config();
		$this->data->page['desc']     = T_('Edit your site general options');
		$this->data->page['haschild'] = false;
	}



	function view_datatable()
	{
		$form_general  = $this->createform('.options', 'general',  true);
		$form_config   = $this->createform('.options', 'config',   true);
		$form_social   = $this->createform('.options', 'social',   true);
		$form_twitter  = $this->createform('.options', 'twitter',  true);
		$form_facebook = $this->createform('.options', 'facebook', true);
		$form_telegram = $this->createform('.options', 'telegram', true);
		$form_sms      = $this->createform('.options', 'sms',      true);
		$form_register = $this->createform('.options', 'account',  true);


		// add languages item
      	foreach (\lib\option::language('list') as $key => $value)
		{
			$form_config->config_defaultLang->child()->id('lang_'.$key)->value($key)->label($value);
		}
		// add sms items
		$form_sms->sms_name->child()->id('sms_kavenegar')->value('kavenegar_api')->label(T_('Kavenegar'));
		$tld_list = ['com', 'org', 'edu', 'net', 'ir'];
		foreach ($tld_list as $key => $tld)
		{
			$form_config->config_defaultTld->child()->id('config_tld_'.$tld)->value($tld)->label($tld);
		}
		// add content list to show for redirect
		foreach (\lib\utility\option::contentList(true) as $key => $value)
		{
			$form_register->account_redirect->child()->id('redirect_'.$key)->value($value)->label(T_($value));
		}

		// give perm list and fill it in default register type
		$myPermList  = $form_register->account_default;
		$myPermNames = \lib\utility\option::permList();
		if(!$myPermNames)
			$myPermNames = [];
		$myPerm      = 1;
		// if list of permission is more than 6 item show in select
		if(count($myPermNames) > 6)
		{
			$myPermList  = $form_register->account_default->type('select');
		}
		// get list of permissions
		foreach ($myPermNames as $key => $value)
		{
			if($myPerm == $key)
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm_'.$key)->selected();
			}
			else
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm_'.$key);
			}
		}

		// get the datatable of options
		$datatable     = $this->model()->draw_options();
		// $datatable['sms']['sms'] = null;

		// fill all forms used in options page


		if(isset($datatable['general']))
		{
			$this->form_fill($form_general,  $datatable['general']);
		}
		if(isset($datatable['config']['config']))
		{
			$this->form_fill($form_config,  $datatable['config']['config']);
			// add default tld to domain name
			if(isset($datatable['config']['config']['meta']['defaultTld']))
			{
				$selectedTld = $datatable['config']['config']['meta']['defaultTld'];
				$form_config->config_domainName->attr('data-after', $selectedTld);
			}
		}
		if(isset($datatable['social']))
		{
			$this->form_fill($form_social,   $datatable['social']);
		}
		if(isset($datatable['social']['twitter']))
		{
			$this->form_fill($form_twitter,  $datatable['social']['twitter']);
		}
		if(isset($datatable['social']['facebook']))
		{
			$this->form_fill($form_facebook, $datatable['social']['facebook']);
		}
		if(isset($datatable['social']['telegram']))
		{
			$this->form_fill($form_telegram, $datatable['social']['telegram']);
		}
		if(isset($datatable['sms']['sms']))
		{
			$this->form_fill($form_sms,      $datatable['sms']['sms']);
		}
		if(isset($datatable['account']['account']))
		{
			$this->form_fill($form_register, $datatable['account']['account']);
		}
	}
}
?>