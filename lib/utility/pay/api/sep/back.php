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


        $State     = isset($_REQUEST['State'])      ? (string) $_REQUEST['State']       : null;
        $StateCode = isset($_REQUEST['StateCode'])  ? (string) $_REQUEST['StateCode']   : null;
        $ResNum    = isset($_REQUEST['ResNum'])     ? (string) $_REQUEST['ResNum']      : null;
        $MID       = isset($_REQUEST['MID'])        ? (string) $_REQUEST['MID']         : null;
        $RefNum    = isset($_REQUEST['RefNum'])     ? (string) $_REQUEST['RefNum']      : null;
        $CID       = isset($_REQUEST['CID'])        ? (string) $_REQUEST['CID']         : null;
        $TRACENO   = isset($_REQUEST['TRACENO'])    ? (string) $_REQUEST['TRACENO']     : null;
        $SecurePan = isset($_REQUEST['SecurePan'])  ? (string) $_REQUEST['SecurePan']   : null;

        if(!$ResNum)
        {
            \dash\log::set('pay:sep:ResNum:verify:not:found');
            \dash\notif::error(T_("The sep payment ResNum not set"));
            return \dash\utility\pay\setting::turn_back();
        }


        \dash\utility\pay\setting::load_banktoken_transaction_id($_token, $ResNum, 'sep');

        $transaction_id  = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:sep:SESSION:transaction_id:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }

        $amount_SESSION  = floatval(\dash\utility\pay\setting::get_plus()) * 10;

        if(!$amount_SESSION)
        {
            \dash\log::set('pay:sep:SESSION:amount:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }

        $sep             = [];
        $sep['RefNum']   = $RefNum;
        $sep['Amount']   = $amount_SESSION;

        $sep['MID']      = \dash\option::config('sep', 'MID');
        $sep['Username'] = \dash\option::config('sep', 'MID');
        $sep['Password'] = \dash\option::config('sep', 'Password');

        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);


        if($State == "OK")
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
                $is_reverse = \dash\utility\pay\api\sep\bank::reverse($sep);

                $payment_response = \dash\utility\pay\api\sep\bank::$payment_response;
                \dash\utility\pay\setting::set_payment_response4($payment_response);

                if($is_reverse)
                {
                    return \dash\utility\pay\verify::bank_error('verify_error');
                }
                else
                {
                    \dash\log::set('sepBankBadBug', ['code' => $transaction_id]);
                    return \dash\utility\pay\verify::bank_error('verify_error');
                }
            }
        }
        else
        {
            return \dash\utility\pay\verify::bank_error('error');
        }
    }
}
?>
