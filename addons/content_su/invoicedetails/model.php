<?php
namespace addons\content_su\invoicedetails;


class model extends \addons\content_su\main\model
{
	public function invoicedetails_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\lib\utility::get('search'))
		{
			$search = \lib\utility::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \lib\db\invoice_details::search($search, $meta);

		return $result;
	}
}
?>
