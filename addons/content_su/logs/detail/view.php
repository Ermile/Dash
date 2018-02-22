<?php
namespace addons\content_su\logs\detail;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();

		$this->data->page['badge']['link'] = $this->url('baseFull'). '/logs';
		$this->data->page['badge']['text'] = T_('Back to Logs list');
	}

	public function view_detail($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		if($id && is_numeric($id))
		{
			$result = \lib\db\logs::get(['id' => $id, 'limit' => 1]);
			$this->data->log_detail = $result;
		}
	}
}
?>