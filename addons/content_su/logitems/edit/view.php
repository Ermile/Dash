<?php
namespace content_su\logitems\edit;

class view extends \addons\content_su\main\view
{
	public function view_edit($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		if($id && is_numeric($id))
		{
			$result = \dash\db\logitems::get(['id' => $id, 'limit' => 1]);
			$this->data->logitem = $result;
		}
	}
}
?>