<?php
namespace content_pay\home;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Pay detail'));
		\dash\data::page_desc(T_('Pay'));
		// \dash\data::page_special(true);


		$result = \dash\data::dataRow();
		if(isset($result['payment_response']))
		{
			if(is_string($result['payment_response']))
			{
				$payment_response = json_decode($result['payment_response'], true);
				\dash\data::payDetail($payment_response);
			}
		}

		if(\dash\permission::supervisor())
		{
			foreach ($result as $key => $value)
			{
				if(in_array($key, ['payment_response', 'payment_response1', 'payment_response2', 'payment_response3', 'payment_response4']))
				{
					$result[$key] = json_decode($value, true);
				}
			}
			\dash\data::dataRow($result);

		}
	}
}
?>