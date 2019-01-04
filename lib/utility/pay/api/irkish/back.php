<?php
namespace dash\utility\pay\api\irkish;


class back
{

    public static function verify($_token)
    {
        if(!\dash\option::config('irkish', 'status'))
        {
            \dash\log::set('pay:irkish:status:false');
            \dash\notif::error(T_("The irkish payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('irkish', 'merchantId'))
        {
            \dash\log::set('pay:irkish:merchantId:not:set');
            \dash\notif::error(T_("The irkish payment merchantId not set"));
            return \dash\utility\pay\setting::turn_back();
        }


        $token         = isset($_REQUEST['token'])          ? (string) $_REQUEST['token']           : null;
        $merchantId    = isset($_REQUEST['merchantId'])     ? (string) $_REQUEST['merchantId']      : null;
        $resultCode    = isset($_REQUEST['resultCode'])     ? (string) $_REQUEST['resultCode']      : null;
        $paymentId     = isset($_REQUEST['paymentId'])      ? (string) $_REQUEST['paymentId']       : null;
        $InvoiceNumber = isset($_REQUEST['InvoiceNumber'])  ? (string) $_REQUEST['InvoiceNumber']   : null;
        $referenceId   = isset($_REQUEST['referenceId'])    ? (string) $_REQUEST['referenceId']     : null;
        $amount        = isset($_REQUEST['amount'])         ? (string) $_REQUEST['amount']          : null;
        $amount        = str_replace(',', '', $amount);

        if(!$token)
        {
            \dash\log::set('pay:irkish:token:verify:not:found');
            \dash\notif::error(T_("The irkish payment token not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!$resultCode)
        {
            \dash\log::set('pay:irkish:resultCode:verify:not:found');
            \dash\notif::error(T_("The irkish payment resultCode not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::load_banktoken($_token, $token, 'irkish');

        $transaction_id = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:irkish:SESSION:transaction_id:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }

        $irkish                    = [];
        $irkish['merchantId']      = \dash\option::config('irkish', 'merchantId');
        $irkish['token']           = $token;
        $irkish['amount']          = $amount;
        $irkish['referenceNumber'] = (string) $referenceId;
        $irkish['sha1Key']         = \dash\option::config('irkish', 'sha1');

        $amount_SESSION  = floatval(\dash\utility\pay\setting::get_plus());

        if(!$amount_SESSION)
        {
            \dash\log::set('pay:irkish:SESSION:amount:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_amount_end($amount / 10);
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);

        if(intval($resultCode) === 100)
        {

            $is_ok = \dash\utility\pay\api\irkish\bank::verify($irkish);

            $payment_response = \dash\utility\pay\api\irkish\bank::$payment_response;

            \dash\utility\pay\setting::set_payment_response3($payment_response);

            if($is_ok)
            {
                \dash\utility\pay\verify::bank_ok($amount /10, $transaction_id);

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

