<?php
namespace addons\content_cp\users;

class controller extends \addons\content_cp\main\controller
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

		\lib\permission::access('cp:user', 'block');


		$property                     = [];
		foreach ($this->fields as $key => $value)
		{
			$property[$value] = ["/.*/", true , $value];
		}

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>