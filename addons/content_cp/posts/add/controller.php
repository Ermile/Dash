<?php
namespace content_cp\posts\add;

class controller extends \addons\content_cp\main\controller
{
	public function ready()
	{
		\dash\permission::access('cp:posts:add', 'block');

		$this->get()->ALL();
		$this->post('add_posts')->ALL();

	}
}
?>