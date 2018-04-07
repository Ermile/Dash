<?php
namespace content_su\sample;

class controller extends \addons\content_su\main\controller
{

	public function ready()
	{
		parent::ready();

		$addr = \dash\url::dir(1);

		if(!$addr)
		{
			return;
		}

		if(is_file(addons.'content_su/sample/template/'.$addr.'.html'))
		{
			$this->display_name     = 'content_su/sample/template/'.$addr.'.html';
			$this->route_check_true = true;
			$this->get()->ALL();
		}
	}

}
?>