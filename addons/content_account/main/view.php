<?php
namespace content_account\main;

class view extends \mvc\view
{
	public function repository()
	{
		$this->data->bodyclass        = 'siftal';
		$this->include->chart         = true;
		$this->data->display['admin'] = 'content_account/main/layout.html';
	}
}
?>