<?php
namespace dash\utility\pay\api\zarinpal;


class back
{

    public static function verify($_token)
    {
        if(!isset($_REQUEST['Authority']) || !isset($_REQUEST['Status']))
        {
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('zarinpal', 'status'))
        {
            \dash\log::set('pay:zarinpal:status:false');
            \dash\notif::error(T_("The zarinpal payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('zarinpal', 'MerchantID'))
        {
            \dash\log::set('pay:zarinpal:MerchantID:not:set');
            \dash\notif::error(T_("The zarinpal payment MerchantID not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        $zarinpal               = [];
        $zarinpal['MerchantID'] = \dash\option::config('zarinpal', 'MerchantID');
        $zarinpal['Authority']  = $_REQUEST['Authority'];

        \dash\utility\pay\setting::load_banktoken($_token, $zarinpal['Authority'], 'zarinpal');

        $transaction_id  = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:zarinpal:SESSION:transaction_id:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }


        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);


        $zarinpal['Amount']  = \dash\utility\pay\setting::get_plus();

        if($_REQUEST['Status'] == 'NOK')
        {
            return \dash\utility\pay\verify::bank_error('cancel');
        }
        else
        {
            $is_ok = \dash\utility\pay\api\zarinpal\bank::verify($zarinpal);

            $payment_response = \dash\utility\pay\api\zarinpal\bank::$payment_response;

            \dash\utility\pay\setting::set_payment_response3($payment_response);

            if($is_ok)
            {
                \dash\utility\pay\verify::bank_ok($zarinpal['Amount'], $transaction_id);

                return \dash\utility\pay\setting::turn_back();
            }
            else
            {
               return \dash\utility\pay\verify::bank_error('verify_error');
            }
        }
    }
}
?>

