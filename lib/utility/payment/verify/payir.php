<?php
namespace dash\utility\payment\verify;


trait payir
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function payir($_args)
    {
        self::config();

        $log_meta =
        [
            'data' => self::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
                'session' => $_SESSION,
            ]
        ];

        if(!\lib\option::config('payir', 'status'))
        {
            \lib\db\logs::set('pay:payir:status:false', self::$user_id, $log_meta);
            \lib\notif::error(T_("The payir payment on this service is locked"));
            return self::turn_back();
        }

        if(!\lib\option::config('payir', 'api'))
        {
            \lib\db\logs::set('pay:payir:api:not:set', self::$user_id, $log_meta);
            \lib\notif::error(T_("The payir payment api not set"));
            return self::turn_back();
        }

        $transId      = isset($_REQUEST['transId'])        ? (string) $_REQUEST['transId']         : null;
        $status       = isset($_REQUEST['status'])         ? (string) $_REQUEST['status']          : null;
        $description  = isset($_REQUEST['description'])    ? (string) $_REQUEST['description']     : null;
        $factorNumber = isset($_REQUEST['factorNumber'])   ? (string) $_REQUEST['factorNumber']    : null;
        $cardNumber   = isset($_REQUEST['cardNumber'])     ? (string) $_REQUEST['cardNumber']      : null;
        $message      = isset($_REQUEST['message'])        ? (string) $_REQUEST['message']         : null;


        if(!$transId)
        {
            \lib\db\logs::set('pay:payir:transId:verify:not:found', self::$user_id, $log_meta);
            \lib\notif::error(T_("The payir payment transId not set"));
            return self::turn_back();
        }

        if(isset($_SESSION['amount']['payir'][$transId]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['payir'][$transId]['transaction_id'];
        }
        else
        {
            \lib\db\logs::set('pay:payir:SESSION:transaction_id:not:found', self::$user_id, $log_meta);
            \lib\notif::error(T_("Your session is lost! We can not find your transaction"));
            return self::turn_back();
        }

        $log_meta['data'] = self::$log_data = $transaction_id;

        $payir            = [];
        $payir['api']     = \lib\option::config('payir', 'api');
        $payir['transId'] = $transId;

        if(isset($_SESSION['amount']['payir'][$transId]['amount']))
        {
            $amount_SESSION  = floatval($_SESSION['amount']['payir'][$transId]['amount']);
        }
        else
        {
            \lib\db\logs::set('pay:payir:SESSION:amount:not:found', self::$user_id, $log_meta);
            \lib\notif::error(T_("Your session is lost! We can not find amount"));
            return self::turn_back();
        }

        $update =
        [
            'amount_end'       => $amount_SESSION / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \lib\db\transactions::update($update, $transaction_id);

        \lib\db\logs::set('pay:payir:pending:request', self::$user_id, $log_meta);

        // $msg = \lib\utility\payment\payment\payir::msg($status);

        if(intval($status) === 1)
        {
            \lib\utility\payment\payment\payir::$user_id = self::$user_id;

            \lib\utility\payment\payment\payir::$log_data = self::$log_data;

            $is_ok = \lib\utility\payment\payment\payir::verify($payir);

            $payment_response = \lib\utility\payment\payment\payir::$payment_response;

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

                    \lib\db\transactions::calc_budget($transaction_id, $amount_SESSION / 10, 0, $update);

                    \lib\db\logs::set('pay:payir:ok:request', self::$user_id, $log_meta);

                    \lib\session::set('payment_verify_status', 'ok');
                    \lib\session::set('payment_verify_amount', $amount_SESSION / 10);

                    unset($_SESSION['amount']['payir'][$transId]);

                    return self::turn_back($transaction_id);
                }
                else
                {
                    \lib\db\logs::set('pay:payir:SESSION:amount:not:found', self::$user_id, $log_meta);
                    \lib\notif::error(T_("Your session is lost! We can not find amount"));
                    return self::turn_back();
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

                \lib\session::set('payment_verify_status', 'verify_error');

                \lib\db\transactions::update($update, $transaction_id);
                \lib\db\logs::set('pay:payir:verify_error:request', self::$user_id, $log_meta);
                return self::turn_back($transaction_id);

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

            \lib\session::set('payment_verify_status', 'error');

            \lib\db\transactions::update($update, $transaction_id);
            \lib\db\logs::set('pay:payir:error:request', self::$user_id, $log_meta);
            return self::turn_back($transaction_id);
        }
    }
}
?>