<?php
namespace content_account\billing\invoice;
use \lib\utility;
use \lib\debug;
use \lib\utility\payment;

class model extends \mvc\model
{

	/**
	 * get invoice data to show
	 */
	public function get_invoice($_args)
	{
		if(!$this->login())
		{
			return false;
		}
		$invoice_id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$invoice_detail = \lib\db\invoices::load($invoice_id, $this->login('id'));
		return $invoice_detail;
	}

	/**
	 * post data and update or insert invoice data
	 */
	public function post_invoice()
	{

	}
}
?>