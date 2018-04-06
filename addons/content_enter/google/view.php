<?php
namespace addons\content_enter\google;


class view extends \addons\content_enter\main\view
{
	public function config()
	{
		parent::config();

		$this->data->auth_url = \lib\social\google::auth_url();

		// auto redirect if url is clean
		if($this->data->auth_url && !\lib\request::get() && !\lib\request::post() && $this->data->googleLogin)
		{
			\lib\redirect::to($this->data->auth_url);
		}


		$this->data->page['title']   = T_('Enter to :name with google', ['name' => $this->data->site['title']]);
		$this->data->page['special'] = true;
		$this->data->page['desc']    = $this->data->page['title'];


	}
}
?>