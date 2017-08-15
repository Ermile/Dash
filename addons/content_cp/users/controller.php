<?php
namespace addons\content_cp\users;

class controller extends \mvc\controller
{
	public $fields =
	[
		'id',
		'user_mobile',
		'user_email',
		'user_pass',
		'user_displayname',
		'user_status',
		'user_parent',
		'user_permission',
		'user_createdate',
		'date_modified',
		'user_username',
		'user_file_url',
		'sort',
		'order',
	];

	public function _route()
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