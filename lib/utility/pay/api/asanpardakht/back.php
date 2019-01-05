<?php
namespace dash\utility\pay\api\asanpardakht;


class back
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function verify($_token)
    {

        if(!\dash\option::config('asanpardakht', 'status'))
        {
            \dash\log::set('pay:asanpardakht:status:false');
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('asanpardakht', 'MerchantID'))
        {
            \dash\log::set('pay:asanpardakht:MerchantID:false');
            \dash\notif::error(T_("The asanpardakht payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        $username = \dash\option::config('asanpardakht', 'Username');
        $password = \dash\option::config('asanpardakht', 'Password');

        $ReturningParams    = isset($_REQUEST['ReturningParams']) ? (string) $_REQUEST['ReturningParams'] : null;

        \dash\utility\pay\api\asanpardakht\bank::set_key_iv();

        $ReturningParams    = \dash\utility\pay\api\asanpardakht\bank::decrypt($ReturningParams);

        $RetArr             = explode(",", $ReturningParams);
        $Amount             = isset($RetArr[0]) ? $RetArr[0] : null;
        $SaleOrderId        = isset($RetArr[1]) ? $RetArr[1] : null;
        $RefId              = isset($RetArr[2]) ? $RetArr[2] : null;
        $ResCode            = isset($RetArr[3]) ? $RetArr[3] : null;
        $ResMessage         = isset($RetArr[4]) ? $RetArr[4] : null;
        $PayGateTranID      = isset($RetArr[5]) ? $RetArr[5] : null;
        $RRN                = isset($RetArr[6]) ? $RetArr[6] : null;
        $LastFourDigitOfPAN = isset($RetArr[7]) ? $RetArr[7] : null;

        \dash\utility\pay\setting::load_banktoken($_token, $RefId, 'asanpardakht');

        $transaction_id  = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:asanpardakht:transaction_id:not:found:verify');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::set_amount_end($Amount / 10);
        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);

        $Amount_Record  = floatval(\dash\utility\pay\setting::get_plus());

        if(!$Amount_Record)
        {
            \dash\log::set('pay:asanpardakht:amount:not:found:verify');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }

        if($Amount_Record != ($Amount / 10))
        {
            \dash\log::set('pay:asanpardakht:Amount_Record:amount:is:not:equals');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }


        if($ResCode == '0' || $ResCode == '00')
        {
            $is_ok = \dash\utility\pay\api\asanpardakht\bank::verify($RetArr);

            $payment_response = \dash\utility\pay\api\asanpardakht\bank::$payment_response;
            \dash\utility\pay\setting::set_payment_response3($payment_response);

            if($is_ok)
            {
                \dash\utility\pay\verify::bank_ok($Amount_Record / 10, $transaction_id);
                return \dash\utility\pay\setting::turn_back();
            }
            else
            {
                return \dash\utility\pay\verify::bank_error('verify_error');
            }
        }
        else
        {
            return \dash\utility\pay\verify::bank_error('error');
        }
    }
}
?>
