<?php
namespace addons\content_account\home;

class view extends \mvc\view
{
	public function config()
	{
		// $this->include->css_main       = false;
		$this->include->css            = false;
		$this->include->js             = false;
		// $this->include->telinput       = true;
		$this->include->css_ermile     = true;
		$this->include->fontawesome    = true;
		$this->data->bodyclass         = 'unselectable';
		$this->data->myform            = 'account';
		$this->data->account           = $this->option('account', null);

		$this->global->cookier         = array( 'domain' => '.'.$this->url->raw,
															 'path'   => '/',
															 'age'    => 'temporary'
															);

		switch ($this->data->module)
		{
			case 'login':
				$this->data->page['desc']	= T_('Login');
				break;

			case 'signup':
				$this->data->page['desc']	= T_('Create an account');
				break;

			case 'verification':
				$this->data->page['desc']	= T_('Verificate');
				break;

			case 'verificationsms':
				$this->data->page['desc']	= T_('Verificate');
				break;

			case 'recovery':
				$this->data->page['desc']	= T_('Recovery');
				break;

			case 'changepass':
				$this->data->page['desc']	= T_('Change password');
				$this->include->telinput	= false;
				$this->global->cookier     = null;
				break;

			case 'smsdelivery':
				$this->data->page['desc']	= T_('SMS Delivery');
				$this->include->telinput	= false;
				$this->global->cookier     = null;
				break;

			case 'smscallback':
				$this->data->page['desc']	= T_('SMS Callback');
				$this->include->telinput	= false;
				$this->global->cookier     = null;
				break;

			default:
				$this->data->page['desc']	= T_('Ermile');
				break;
		}

		$this->global->title = $this->data->page['desc'].' | '.$this->data->site['title'];
		$form                = $this->createform('.'.$this->data->myform, $this->data->module);
	}
}
?>