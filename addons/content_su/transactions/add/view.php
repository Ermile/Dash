<?php
namespace addons\content_su\transactions\add;

class view extends \addons\content_su\transactions\view
{
	public function config()
	{
		parent::config();
		$this->data->modulePath = $this->url('baseFull');

		$this->data->page['title'] = T_("Add new transactions");
		$this->data->page['desc'] = T_("Add new transactions for every one");


		$this->data->page['badge']['link'] = $this->data->modulePath. '/transactions';
		$this->data->page['badge']['text'] = T_('Back to transactions list');

	}


	public function view_add($_args)
	{
		$data = $this->model()->loadMyTransaction($_args);

		if(isset($data))
		{
			if(isset($data['user_id']))
			{
				$this->data->get_mobile = \lib\db\users::get_mobile($data['user_id']);
			}

			$this->data->transaction_record = $data;
		}


		if(\lib\utility::get('search'))
		{
			$url = $this->url('full');
			$url = preg_replace("/search\=(.*)(\/|)/", "search=". \lib\utility::get('search'), $url);

			$this->redirector($url)->redirect();
		}


		if(isset($_args->get("search")[0]))
		{
			$this->data->get_search = $_args->get("search")[0];
		}


		if(\lib\utility::get('mobile'))
		{
			$this->data->get_mobile = \lib\utility\filter::mobile(\lib\utility::get('mobile'));
		}
	}
}
?>