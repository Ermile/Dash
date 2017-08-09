<?php
namespace addons\content_cp\profile;

use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\home\model
{
	/**
	 * Update profile data
	 * @return run update query and no return value
	 */
	function put_profile()
	{
		$qry = $this->sql()->table('users')->where('id', $this->login('id'))
			->set('user_mobile',      utility::post('mobile'))
			->set('user_email',       utility::post('email'))
			->set('user_displayname', utility::post('displayname'));
		$qry->update();

		$this->commit(function()
		{
			debug::true(T_("Update Successfully"));
			// $this->redirector()->set_url($_module.'/edit='.$_postId);
		});

		// if a query has error or any error occour in any part of codes, run roolback
		$this->rollback(function()
		{
			debug::title(T_("Transaction error").': ');
		} );
	}
}
?>