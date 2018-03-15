<?php
namespace addons\content_su\cronjob;

class model extends \addons\content_su\main\model
{
	public function post_cronjob()
	{
		if(\lib\request::post('active'))
		{
			\lib\utility\cronjob\options::active();
			\lib\debug::true(T_("Your cronjob is actived"));
		}
		else
		{
			\lib\utility\cronjob\options::deactive();
			\lib\debug::warn(T_("Your cronjob is deactived"));
		}

		$this->redirector(\lib\url::pwd());
	}
}
?>
