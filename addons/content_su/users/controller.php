<?php
namespace addons\content_su\users;

class controller extends \addons\content_su\main\controller
{
	public $fields =
	[
		'id',
		'mobile',
		'email',
		'password',
		'displayname',
		'status',
		'parent',
		'permission',
		'datecreated',
		'datemodified',
		'username',
		'fileurl',
		'sort',
		'order',
	];

	public function ready()
	{
		parent::ready();

		$property                     = [];
		foreach ($this->fields as $key => $value)
		{
			$property[$value] = ["/.*/", true , $value];
		}

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>