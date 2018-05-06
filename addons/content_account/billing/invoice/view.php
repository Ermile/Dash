<?php
namespace content_account\billing\invoice;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Invoice Detail"));
		\dash\data::page_desc(T_("Check invoice and detail of it"));
		$invoice_detail = \dash\db\invoices::load(\dash\request::get('id'), \dash\user::id());
		\dash\data::invoice($invoice_detail);
	}
}
?>