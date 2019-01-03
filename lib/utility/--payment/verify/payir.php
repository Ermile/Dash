<?php
namespace dash\utility\payment\verify;


class payir
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function payir($_args)
    {
        \dash\utility\payment\verify::config();

        $log_meta =
        [
            'data' => \dash\utility\payment\verify::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
            ]
        ];

        if(!\dash\option::config('payir', 'status'))
        {
            \dash\db\logs::set('pay:payir:status:false', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The payir payment on this service is locked"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(!\dash\option::config('payir', 'api'))
        {
            \dash\db\logs::set('pay:payir:api:not:set', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The payir payment api not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        $transId      = isset($_REQUEST['transId'])        ? (string) $_REQUEST['transId']         : null;
        $status       = isset($_REQUEST['status'])         ? (string) $_REQUEST['status']          : null;
        $description  = isset($_REQUEST['description'])    ? (string) $_REQUEST['description']     : null;
        $factorNumber = isset($_REQUEST['factorNumber'])   ? (string) $_REQUEST['factorNumber']    : null;
        $cardNumber   = isset($_REQUEST['cardNumber'])     ? (string) $_REQUEST['cardNumber']      : null;
        $message      = isset($_REQUEST['message'])        ? (string) $_REQUEST['message']         : null;


        if(!$transId)
        {
            \dash\db\logs::set('pay:payir:transId:verify:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The payir payment transId not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(isset($_SESSION['amount']['payir'][$transId]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['payir'][$transId]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:payir:SESSION:transaction_id:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\payment\verify::turn_back();
        }

        $log_meta['data'] = \dash\utility\payment\verify::$log_data = $transaction_id;

        $payir            = [];
        $payir['api']     = \dash\option::config('payir', 'api');
        $payir['transId'] = $transId;

        if(isset($_SESSION['amount']['payir'][$transId]['amount']))
        {
            $amount_SESSION  = floatval($_SESSION['amount']['payir'][$transId]['amount']);
        }
        else
        {
            \dash\db\logs::set('pay:payir:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        $update =
        [
            'amount_end'       => $amount_SESSION / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \dash\utility\payment\transactions::update($update, $transaction_id);

        \dash\db\logs::set('pay:payir:pending:request', \dash\utility\payment\verify::$user_id, $log_meta);

        // $msg = \dash\utility\payment\payment\payir::msg($status);

        if(intval($status) === 1)
        {
            \dash\utility\payment\payment\payir::$user_id = \dash\utility\payment\verify::$user_id;

            \dash\utility\payment\payment\payir::$log_data = \dash\utility\payment\verify::$log_data;

            $is_ok = \dash\utility\payment\payment\payir::verify($payir);

            $payment_response = \dash\utility\payment\payment\payir::$payment_response;

            $log_meta['meta']['payment_response'] = (array) $payment_response;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            if(isset($is_ok['status']) && intval($is_ok['status']) === 1 )
            {
                if(isset($is_ok['amount']) && intval($is_ok['amount']) === intval($amount_SESSION))
                {
                    $update =
                    [
                        'amount_end'       => $amount_SESSION / 10,
                        'condition'        => 'ok',
                        'verify'           => 1,
                        'payment_response' => $payment_response,
                    ];

                    \dash\utility\payment\verify::$final_verify         = true;
                    \dash\utility\payment\verify::$final_transaction_id = $transaction_id;

                    \dash\utility\payment\transactions::calc_budget($transaction_id, $amount_SESSION / 10, 0, $update);

                    \dash\db\logs::set('pay:payir:ok:request', \dash\utility\payment\verify::$user_id, $log_meta);

                    \dash\session::set('payment_verify_status', 'ok');
                    \dash\session::set('payment_verify_amount', $amount_SESSION / 10);

                    unset($_SESSION['amount']['payir'][$transId]);

                    return \dash\utility\payment\verify::turn_back($transaction_id);
                }
                else
                {
                    \dash\db\logs::set('pay:payir:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
                    \dash\notif::error(T_("Your session is lost! We can not find amount"));
                    return \dash\utility\payment\verify::turn_back();
                }
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
                \dash\db\logs::set('pay:payir:verify_error:request', \dash\utility\payment\verify::$user_id, $log_meta);
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
            \dash\db\logs::set('pay:payir:error:request', \dash\utility\payment\verify::$user_id, $log_meta);
            return \dash\utility\payment\verify::turn_back($transaction_id);
        }
    }
}
?>