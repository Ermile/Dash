<?php
namespace content_account\billing\detail;
use \lib\utility;
use \lib\debug;
use \lib\utility\payment;

class model extends \mvc\model
{

	/**
	 * get detail data to show
	 */
	public function get_detail($_args)
	{
		if(!$this->login())
		{
			return false;
		}
	}



	/**
	 * post data and update or insert detail data
	 */
	public function post_detail()
	{

	}
}
?>