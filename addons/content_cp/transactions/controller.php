<?php
namespace addons\content_cp\transactions;

class controller extends \mvc\controller
{
	public function _route()
	{

		\lib\permission::access('cp:transaction', 'block');

		$property                 = [];

		$property['id']           = ["/.*/", true , 'id'];
		$property['name']         = ["/.*/", true , 'name'];
		$property['title']        = ["/.*/", true , 'title'];
		$property['status']       = ["/.*/", true , 'status'];
		$property['type']         = ["/.*/", true , 'type'];
		$property['unit']         = ["/.*/", true , 'unit'];
		$property['ninus']        = ["/.*/", true , 'ninus'];
		$property['plus']         = ["/.*/", true , 'plus'];
		$property['budgetbefore'] = ["/.*/", true , 'budgetbefore'];
		$property['budget']       = ["/.*/", true , 'budget'];
		$property['date']         = ["/.*/", true , 'date'];
		$property['order']        = ["/.*/", true , 'order'];
		$property['sort']         = ["/.*/", true , 'sort'];
		$property['search']       = ["/.*/", true , 'search'];
		$property['mobile']       = ["/.*/", true , 'mobile'];
		$property['user']       = ["/.*/", true , 'user'];
		$property['caller']       = ["/.*/", true , 'caller'];

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>