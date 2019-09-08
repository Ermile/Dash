<?php
namespace dash\utility\pay\api\sep;


class back
{

    public static function verify($_token)
    {
        if(!\dash\option::config('sep', 'status'))
        {
            \dash\log::set('pay:sep:status:false');
            \dash\notif::error(T_("The sep payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('sep', 'MID'))
        {
            \dash\log::set('pay:sep:MID:null');
            \dash\notif::error(T_("The sep payment MID not set"));
            return \dash\utility\pay\setting::turn_back();
        }


        $State     = isset($_POST['State'])      ? (string) $_POST['State']       : null;
        $StateCode = isset($_POST['StateCode'])  ? (string) $_POST['StateCode']   : null;
        $ResNum    = isset($_POST['ResNum'])     ? (string) $_POST['ResNum']      : null;
        $MID       = isset($_POST['MID'])        ? (string) $_POST['MID']         : null;
        $RefNum    = isset($_POST['RefNum'])     ? (string) $_POST['RefNum']      : null;
        $CID       = isset($_POST['CID'])        ? (string) $_POST['CID']         : null;
        $TRACENO   = isset($_POST['TRACENO'])    ? (string) $_POST['TRACENO']     : null;
        $SecurePan = isset($_POST['SecurePan'])  ? (string) $_POST['SecurePan']   : null;

        if(!$ResNum)
        {
            \dash\log::set('pay:sep:ResNum:verify:not:found');
            \dash\notif::error(T_("The sep payment ResNum not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::load_banktoken($_token, $ResNum, 'sep');

        $transaction_id  = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:sep:SESSION:transaction_id:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }



        $amount_SESSION  = floatval(\dash\utility\pay\setting::get_plus());

        if(!$amount_SESSION)
        {
            \dash\log::set('pay:sep:SESSION:amount:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }

        $sep           = [];
        $sep['RefNum'] = $RefNum;
        $sep['Amount'] = $amount_SESSION;
        $sep['MID']    = \dash\option::config('sep', 'MID');

        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);


        if(intval($State) == "OK")
        {

            $is_ok = \dash\utility\pay\api\sep\bank::verify($sep);

            $payment_response = \dash\utility\pay\api\sep\bank::$payment_response;

            \dash\utility\pay\setting::set_payment_response3($payment_response);

            if($is_ok)
            {
                \dash\utility\pay\verify::bank_ok($amount_SESSION, $transaction_id);

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
