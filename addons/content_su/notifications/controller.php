<?php
namespace content_su\notifications;

class controller extends \addons\content_su\main\controller
{
	public $fields =
	[
		'id',
		'user_id',
		'user_idsender',
		'title',
		'content',
		'url',
		'read',
		'star',
		'status',
		'category',
		'createdate',
		'senddate',
		'deliverdate',
		'expiredate',
		'readdate',
		'gateway',
		'auto',
		'datemodified',
		'desc',
		'meta',
		'sort',
		'order',
		'search',
		'data',
	];

	public function ready()
	{
		parent::ready();

		$property                 = [];
		foreach ($this->fields as $key => $value)
		{
			$property[$value] = ["/.*/", true, $value];
		}

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>