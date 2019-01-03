<?php
namespace dash\utility\payment\verify;


class zarinpal
{
    /**
     * check payment of zarinpal
     *
     * @param      <type>   $_args  The arguments
     *
     * @return     boolean  ( description_of_the_return_value )
     */
    public static function zarinpal($_args)
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

        if(!isset($_args['get']['Authority']) || !isset($_args['get']['Status']))
        {
            return \dash\utility\payment\verify::turn_back();
        }

        if(!\dash\option::config('zarinpal', 'status'))
        {
            \dash\db\logs::set('pay:zarinpal:status:false', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The zarinpal payment on this service is locked"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(!\dash\option::config('zarinpal', 'MerchantID'))
        {
            \dash\db\logs::set('pay:zarinpal:MerchantID:not:set', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The zarinpal payment MerchantID not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        $zarinpal = [];
        $zarinpal['MerchantID'] = \dash\option::config('zarinpal', 'MerchantID');

        $zarinpal['Authority']  = $_args['get']['Authority'];


        if(isset($_SESSION['amount']['zarinpal'][$zarinpal['Authority']]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['zarinpal'][$zarinpal['Authority']]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:zarinpal:SESSION:transaction_id:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\payment\verify::turn_back();
        }

        $log_meta['data'] = \dash\utility\payment\verify::$log_data = $transaction_id;

        $update =
        [
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];
        \dash\utility\payment\transactions::update($update, $transaction_id);

        \dash\db\logs::set('pay:zarinpal:pending:request', \dash\utility\payment\verify::$user_id, $log_meta);

        if(isset($_SESSION['amount']['zarinpal'][$zarinpal['Authority']]['amount']))
        {
            $zarinpal['Amount']  = $_SESSION['amount']['zarinpal'][$zarinpal['Authority']]['amount'];
        }
        else
        {
            \dash\db\logs::set('pay:zarinpal:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        if($_args['get']['Status'] == 'NOK')
        {
            $update =
            [
                'amount_end'       => $zarinpal['Amount'],
                'condition'        => 'cancel',
                'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
            ];
            \dash\utility\payment\transactions::update($update, $transaction_id);
            \dash\db\logs::set('pay:zarinpal:cancel:request', \dash\utility\payment\verify::$user_id, $log_meta);
            return \dash\utility\payment\verify::turn_back($transaction_id);
        }
        else
        {
            \dash\utility\payment\payment\zarinpal::$user_id = \dash\utility\payment\verify::$user_id;
            \dash\utility\payment\payment\zarinpal::$log_data = \dash\utility\payment\verify::$log_data;

            $is_ok = \dash\utility\payment\payment\zarinpal::verify($zarinpal);

            $payment_response = \dash\utility\payment\payment\zarinpal::$payment_response;

            $log_meta['meta']['payment_response'] = (array) $payment_response;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            if($is_ok)
            {
                $update =
                [
                    'amount_end'       => $zarinpal['Amount'],
                    'condition'        => 'ok',
                    'verify'           => 1,
                    'payment_response' => $payment_response,
                ];

                \dash\utility\payment\verify::$final_verify         = true;
                \dash\utility\payment\verify::$final_transaction_id = $transaction_id;

                \dash\utility\payment\transactions::calc_budget($transaction_id, $zarinpal['Amount'], 0, $update);

                \dash\db\logs::set('pay:zarinpal:ok:request', \dash\utility\payment\verify::$user_id, $log_meta);

                \dash\session::set('payment_verify_amount', $zarinpal['Amount']);
                \dash\session::set('payment_verify_status', 'ok');

                unset($_SESSION['amount']['zarinpal'][$zarinpal['Authority']]);

                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
            else
            {
                $update =
                [
                    'amount_end'       => $zarinpal['Amount'],
                    'condition'        => 'verify_error',
                    'payment_response' => $payment_response,
                ];
                \dash\session::set('payment_verify_status', 'verify_error');
                \dash\utility\payment\transactions::update($update, $transaction_id);
                \dash\db\logs::set('pay:zarinpal:verify_error:request', \dash\utility\payment\verify::$user_id, $log_meta);
                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
        }
    }
}
?>