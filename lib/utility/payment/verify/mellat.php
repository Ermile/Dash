<?php
namespace dash\utility\payment\verify;


class mellat
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function mellat($_args)
    {
        \dash\utility\payment\verify::config();

        $log_meta =
        [
            'data' => \dash\utility\payment\verify::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
                'session' => $_SESSION,
            ]
        ];

        if(!\dash\option::config('mellat', 'status'))
        {
            \dash\db\logs::set('pay:mellat:status:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment on this service is locked"));
            return false;
        }

        if(!\dash\option::config('mellat', 'TerminalId'))
        {
            \dash\db\logs::set('pay:mellat:TerminalId:null', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment TerminalId not set"));
            return false;
        }

        if(!\dash\option::config('mellat', 'UserName'))
        {
            \dash\db\logs::set('pay:mellat:UserName:null', $_user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment UserName not set"));
            return false;
        }

        $RefId           = isset($_REQUEST['RefId'])              ? (string) $_REQUEST['RefId']               : null;
        $ResCode         = isset($_REQUEST['ResCode'])            ? (string) $_REQUEST['ResCode']             : null;
        $saleOrderId     = isset($_REQUEST['saleOrderId'])        ? (string) $_REQUEST['saleOrderId']         : null;
        $SaleReferenceId = isset($_REQUEST['SaleReferenceId'])    ? (string) $_REQUEST['SaleReferenceId']     : null;


        if(!$RefId)
        {
            \dash\db\logs::set('pay:mellat:RefId:verify:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The mellat payment RefId not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(isset($_SESSION['amount']['mellat'][$RefId]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['mellat'][$RefId]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:mellat:SESSION:transaction_id:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\payment\verify::turn_back();
        }


        $mellat                    = [];
        $mellat['terminalId']      = \dash\option::config('mellat', 'TerminalId');
        $mellat['userName']        = \dash\option::config('mellat', 'UserName');
        $mellat['userPassword']    = \dash\option::config('mellat', 'UserPassword');
        $mellat['saleOrderId']     = $saleOrderId;
        $mellat['saleReferenceId'] = $SaleReferenceId;
        $mellat['orderId']         = $transaction_id;

        $log_meta['data'] = \dash\utility\payment\verify::$log_data = $transaction_id;

        if(isset($_SESSION['amount']['mellat'][$RefId]['amount']))
        {
            $amount_SESSION  = floatval($_SESSION['amount']['mellat'][$RefId]['amount']);
        }
        else
        {
            \dash\db\logs::set('pay:mellat:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        $update =
        [
            'amount_end'       => $amount / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \dash\utility\payment\transactions::update($update, $transaction_id);

        \dash\db\logs::set('pay:mellat:pending:request', \dash\utility\payment\verify::$user_id, $log_meta);

        // $msg = \dash\utility\payment\payment\mellat::msg($ResCode);

        if(intval($ResCode) === 0)
        {
            \dash\utility\payment\payment\mellat::$user_id = \dash\utility\payment\verify::$user_id;

            \dash\utility\payment\payment\mellat::$log_data = \dash\utility\payment\verify::$log_data;

            $is_ok = \dash\utility\payment\payment\mellat::verify($mellat);

            $payment_response = \dash\utility\payment\payment\mellat::$payment_response;

            $log_meta['meta']['payment_response'] = (array) $payment_response;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            if($is_ok)
            {
                $update =
                [
                    'amount_end'       => $amount_SESSION / 10,
                    'condition'        => 'ok',
                    'verify'           => 1,
                    'payment_response' => $payment_response,
                ];

                \dash\utility\payment\transactions::calc_budget($transaction_id, $amount_SESSION / 10, 0, $update);

                \dash\db\logs::set('pay:mellat:ok:request', \dash\utility\payment\verify::$user_id, $log_meta);

                \dash\session::set('payment_verify_status', 'ok');
                \dash\session::set('payment_verify_amount', $amount_SESSION / 10);

                unset($_SESSION['amount']['mellat'][$RefId]);

                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
            else
            {
                $update =
                [
                    'amount_end'       => $amount_SESSION / 10,
                    'condition'        => 'verify_error',
                    'payment_response' => $payment_response,
                ];

                \dash\session::set('payment_verify_status', 'verify_error');

                \dash\utility\payment\transactions::update($update, $transaction_id);
                \dash\db\logs::set('pay:mellat:verify_error:request', \dash\utility\payment\verify::$user_id, $log_meta);
                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
        }
        else
        {
            $update =
            [
                'amount_end'       => $amount_SESSION / 10,
                'condition'        => 'error',
                'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
            ];

            \dash\session::set('payment_verify_status', 'error');

            \dash\utility\payment\transactions::update($update, $transaction_id);
            \dash\db\logs::set('pay:mellat:error:request', \dash\utility\payment\verify::$user_id, $log_meta);
            return \dash\utility\payment\verify::turn_back($transaction_id);
        }
    }
}
?>