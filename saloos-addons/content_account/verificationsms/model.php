<?php
namespace addons\content_account\verificationsms;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_verificationsms()
	{
		$mymobile   = utility\cookie::read('mobile');

		$tmp_result	= $this->sql()->tableSmss ()
						->whereSms_from           ($mymobile)
						->andSms_type             ('receive')
						->andSms_status           ('enable')
						->select();

		if($tmp_result->num()==1)
			$this->put_changeSmsStatus($mymobile);

		else
			debug::warn(T_('we are waiting for your message!'));
	}

	function put_changeSmsStatus($mymobile)
	{
		$qry		= $this->sql()->tableSmss ()
						->setSms_status        ('expire')
						->whereSms_from        ($mymobile)
						->andSms_type          ('receive')
						->andSms_status        ('enable');
		$sql		= $qry->update();


		// ======================================================
		// you can manage next event with one of these variables,
		// commit for successfull and rollback for failed
		//
		// if query run without error means commit
		$this->commit(function()
		{
			debug::true(T_('we receive your message and your account is now verifited.'));
		});

		// if a query has error or any error occour in any part of codes, run roolback
		$this->rollback(function()
		{
			debug::error(T_('error on verify your code!'));
		} );
	}
}
?>