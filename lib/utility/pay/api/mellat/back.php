<?php
namespace dash\utility\pay\api\mellat;


class back
{

    public static function verify($_token)
    {
        if(!\dash\option::config('mellat', 'status'))
        {
            \dash\log::set('pay:mellat:status:false');
            \dash\notif::error(T_("The mellat payment on this service is locked"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('mellat', 'TerminalId'))
        {
            \dash\log::set('pay:mellat:TerminalId:null');
            \dash\notif::error(T_("The mellat payment TerminalId not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        if(!\dash\option::config('mellat', 'UserName'))
        {
            \dash\log::set('pay:mellat:UserName:null');
            \dash\notif::error(T_("The mellat payment UserName not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        $RefId           = isset($_REQUEST['RefId'])              ? (string) $_REQUEST['RefId']               : null;
        $ResCode         = isset($_REQUEST['ResCode'])            ? (string) $_REQUEST['ResCode']             : null;
        $SaleOrderId     = isset($_REQUEST['SaleOrderId'])        ? (string) $_REQUEST['SaleOrderId']         : null;
        $SaleReferenceId = isset($_REQUEST['SaleReferenceId'])    ? (string) $_REQUEST['SaleReferenceId']     : null;
        // RefId: 123
        // ResCode: 123
        // SaleOrderId: 123
        // SaleReferenceId: 123
        // CardHolderInfo: 123
        // CardHolderPan: 123
        // CardHolderInfo: 123
        // CardHolderPan: 123


        if(!$RefId)
        {
            \dash\log::set('pay:mellat:RefId:verify:not:found');
            \dash\notif::error(T_("The mellat payment RefId not set"));
            return \dash\utility\pay\setting::turn_back();
        }

        \dash\utility\pay\setting::load_banktoken($_token, $RefId, 'mellat');

        $transaction_id  = \dash\utility\pay\setting::get_id();

        if(!$transaction_id)
        {
            \dash\log::set('pay:mellat:SESSION:transaction_id:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\pay\setting::turn_back();
        }


        $mellat                    = [];
        $mellat['terminalId']      = \dash\option::config('mellat', 'TerminalId');
        $mellat['userName']        = \dash\option::config('mellat', 'UserName');
        $mellat['userPassword']    = \dash\option::config('mellat', 'UserPassword');
        $mellat['saleOrderId']     = $SaleOrderId;
        $mellat['saleReferenceId'] = $SaleReferenceId;
        $mellat['orderId']         = $transaction_id;


        $amount_SESSION  = floatval(\dash\utility\pay\setting::get_plus());

        if(!$amount_SESSION)
        {
            \dash\log::set('pay:mellat:SESSION:amount:not:found');
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\pay\setting::turn_back();
        }


        \dash\utility\pay\setting::set_condition('pending');
        \dash\utility\pay\setting::set_payment_response2($_REQUEST);
        \dash\utility\pay\setting::save(true);


        if(intval($ResCode) === 0)
        {

            $is_ok = \dash\utility\pay\api\mellat\bank::verify($mellat);

            $payment_response = \dash\utility\pay\api\mellat\bank::$payment_response;

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
