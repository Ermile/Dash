<?php
namespace dash\utility\pay\api\payir;


class back
{

    public static function verify()
    {
        if(!\dash\option::config('payir', 'status'))
        {
            \dash\db\logs::set('pay:payir:status:false');
            \dash\notif::error(T_("The payir payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('payir', 'api'))
        {
            \dash\db\logs::set('pay:payir:api:not:set');
            \dash\notif::error(T_("The payir payment api not set"));
            return \dash\utility\pay\setting::turn_back();
        }


        $transId      = isset($_REQUEST['transId'])        ? (string) $_REQUEST['transId']         : null;
        $status       = isset($_REQUEST['status'])         ? (string) $_REQUEST['status']          : null;
        $description  = isset($_REQUEST['description'])    ? (string) $_REQUEST['description']     : null;
        $factorNumber = isset($_REQUEST['factorNumber'])   ? (string) $_REQUEST['factorNumber']    : null;
        $cardNumber   = isset($_REQUEST['cardNumber'])     ? (string) $_REQUEST['cardNumber']      : null;
        $message      = isset($_REQUEST['message'])        ? (string) $_REQUEST['message']         : null;


        if(!$transId)
        {
            \dash\db\logs::set('pay:payir:transId:verify:not:found');
            \dash\notif::error(T_("The payir payment transId not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::load_banktoken($transId, 'payir');

        if(\dash\utility\pay\setting::get_id())
        {
            $transaction_id  = \dash\utility\pay\setting::get_id();
        }
        else
        {
            \dash\db\logs::set('pay:payir:transaction_id:not:found:verify');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }

        $payir            = [];
        $payir['api']     = \dash\option::config('payir', 'api');
        $payir['transId'] = $transId;

        if(\dash\utility\pay\setting::get_price())
        {
            $amount  = floatval(\dash\utility\pay\setting::get_price()) * 10;
        }
        else
        {

            \dash\utility\pay\setting::set_condition('error');
            \dash\utility\pay\setting::save();

            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }


        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save();

        if(intval($status) === 1)
        {
            $is_ok = \dash\utility\pay\api\payir\bank::verify($payir);

            $payment_response = \dash\utility\pay\api\payir\bank::$payment_response;

            \dash\utility\pay\setting::set_payment_response3($payment_response);

            if(isset($is_ok['status']) && intval($is_ok['status']) === 1)
            {
                if(isset($is_ok['amount']) && intval($is_ok['amount']) === intval($amount))
                {

                    \dash\utility\pay\setting::set_condition('ok');

                    \dash\utility\pay\setting::set_amount_end($amount / 10);

                    \dash\utility\pay\setting::set_verify(1);

                    \dash\utility\pay\setting::set_budget_field();

                    \dash\utility\pay\setting::save();

                    \dash\utility\pay\transactions::final_verify($transaction_id);

                    return \dash\utility\pay\setting::turn_back();
                }
                else
                {
                    \dash\db\logs::set('pay:payir:amount:not:found:verify');
                    \dash\notif::error(T_("Your session is lost! We can not find amount"));
                    return \dash\utility\pay\setting::turn_back();
                }
            }
            else
            {

                \dash\utility\pay\setting::set_condition('verify_error');

                \dash\utility\pay\setting::set_verify(0);

                \dash\utility\pay\setting::save();

                return \dash\utility\pay\setting::turn_back();
            }
        }
        else
        {
            \dash\utility\pay\setting::set_condition('error');

            \dash\utility\pay\setting::set_verify(0);

            \dash\utility\pay\setting::save();

            return \dash\utility\pay\setting::turn_back();
        }
    }
}
?>