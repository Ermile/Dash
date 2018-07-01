<?php
namespace content_su\update;

class model
{
	public static function post()
	{
		$gitPass = \dash\request::post('gitPass');
		if(!$gitPass)
		{
			\dash\notif::error(T_("Please fill the git password"), 'gitPass');
			return false;
		}

		$result = \content_su\update\controller::gitUpdate('all', $gitPass);
		\dash\session::set('lastUpdateGitResult', $result);
		\dash\redirect::to(\dash\url::this().'?result=true');

	}


}
?>