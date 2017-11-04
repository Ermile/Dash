<?php
namespace content_account\main;

class view extends \mvc\view
{


	/**
	 * config
	 */
	public function repository()
	{
		$this->data->bodyclass = 'fixed unselectable siftal';
		$this->include->css    = true;
		$this->include->chart  = true;
		$this->data->display['admin']        = 'content_account/main/layout.html';

	}
}
?>