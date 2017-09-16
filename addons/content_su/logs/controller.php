<?php
namespace addons\content_su\logs;

class controller extends \addons\content_su\main\controller
{
	public $fields =
	[
		'id',
		'logitem_id',
		'type',
		'caller',
		'title',
		'priority',
		'user_id',
		'data',
		'status',
		'createdate',
		'datemodified',
		'sort',
		'order',
		'search',
		'data',
	];

	public function _route()
	{
		parent::_route();

		$property                 = [];
		foreach ($this->fields as $key => $value)
		{
			$property[$value] = ["/.*/", true, $value];
		}

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>